<?php

namespace App\Http\Controllers;

use App\Models\FinPlan;
use App\Models\FinCentroCosto;
use App\Models\FinClasificadorItem;
use App\Services\BudgetPlanningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetProgrammingController extends Controller
{
    protected $planningService;

    public function __construct(BudgetPlanningService $planningService)
    {
        $this->planningService = $planningService;
    }

    /**
     * Resumen de programación vs ejecución.
     */
    public function index()
    {
        $planes = FinPlan::orderBy('anio', 'desc')->orderBy('version', 'desc')->get();

        // Totales globales para 2026 (o el año más reciente con plan aprobado)
        $latestApproved = FinPlan::where('estado', 'aprobado')->orderBy('anio', 'desc')->first();
        $totalProgramado = 0;
        $totalEjecutado = 0;
        $porcentajeGlobal = 0;

        if ($latestApproved) {
            $stats = \Illuminate\Support\Facades\DB::table('vw_fin_plan_vs_ejecucion')
                ->where('anio', $latestApproved->anio)
                ->select(
                    \Illuminate\Support\Facades\DB::raw('SUM(monto_programado) as total_prog'),
                    \Illuminate\Support\Facades\DB::raw('SUM(monto_devengado) as total_ejec')
                )->first();

            $totalProgramado = $stats->total_prog ?? 0;
            $totalEjecutado = $stats->total_ejec ?? 0;
            $porcentajeGlobal = $totalProgramado > 0 ? ($totalEjecutado / $totalProgramado) * 100 : 0;
        }

        $centrosConPresupuesto = \App\Models\FinPlanItem::whereHas('plan', function ($q) {
            $q->where('estado', 'aprobado');
        })->distinct('centro_costo_id')->count();

        return view('programacion.index', compact('planes', 'totalProgramado', 'totalEjecutado', 'porcentajeGlobal', 'centrosConPresupuesto', 'latestApproved'));
    }

    /**
     * Muestra el detalle de un plan.
     */
    public function show($id)
    {
        $plan = FinPlan::with(['items.clasificadorItem', 'items.centroCosto', 'items.mensualizaciones'])->findOrFail($id);

        // 1. Obtener todos los IDs de clasificadores programados (leaf nodes)
        $leafClasificadorIds = $plan->items->pluck('clasificador_item_id')->unique();

        // 2. Obtener toda la jerarquía necesaria (ascendentes) para estos leaf nodes
        $allHierarchyIds = collect();
        foreach ($leafClasificadorIds as $id) {
            $curr = \App\Models\FinClasificadorItem::find($id);
            while ($curr) {
                $allHierarchyIds->push($curr->id);
                $curr = $curr->parent;
            }
        }
        $allHierarchyIds = $allHierarchyIds->unique();

        // 3. Cargar todos los clasificadores de la jerarquía
        $clasificadores = \App\Models\FinClasificadorItem::whereIn('id', $allHierarchyIds)
            ->orderBy('codigo')
            ->get();

        // 4. Obtener ejecución real (igual que antes)
        $codigos = $clasificadores->pluck('codigo')->unique();
        $cc_ids = $plan->items->pluck('centro_costo_id')->unique();

        $ejecucion = \Illuminate\Support\Facades\DB::table('fin_ejecucion_fact')
            ->where('anio', $plan->anio)
            ->whereIn('codigo_completo', $codigos)
            ->select('codigo_completo', \Illuminate\Support\Facades\DB::raw('SUM(devengado) as ejecutado'))
            ->groupBy('codigo_completo')
            ->get()
            ->keyBy('codigo_completo');

        // 5. Construir el árbol de visualización
        // Agrupamos items por clasificador para fácil acceso
        $itemsByClasificador = $plan->items->groupBy('clasificador_item_id');

        $treeData = $clasificadores->map(function ($cl) use ($itemsByClasificador, $ejecucion) {
            $planItems = $itemsByClasificador->get($cl->id, collect());

            return (object) [
                'clasificador' => $cl,
                'is_leaf' => $cl->nivel === 5 || !FinClasificadorItem::where('parent_id', $cl->id)->exists(),
                'monto_programado' => $planItems->sum('monto_anual'),
                'ejecutado' => $ejecucion->get($cl->codigo)->ejecutado ?? 0,
                'items' => $planItems // Solo las hojas tienen items específicos con CC
            ];
        });

        // 6. Roll-up de montos (de abajo hacia arriba)
        // Como están ordenados por código y nivel, podemos hacerlo iterando niveles
        for ($nivel = 4; $nivel >= 1; $nivel--) {
            foreach ($treeData->where('clasificador.nivel', $nivel) as $padre) {
                $hijos = $treeData->filter(function ($item) use ($padre) {
                    return $item->clasificador->parent_id === $padre->clasificador->id;
                });

                $padre->monto_programado = $hijos->sum('monto_programado');
                // Nota: La ejecución ya viene agregada desde la base si usamos los códigos cortos, 
                // pero por seguridad también podríamos sumarla aquí si la vista no lo hace.
            }
        }

        return view('programacion.show', compact('plan', 'treeData'));
    }

    /**
     * Muestra el formulario para crear un nuevo plan.
     */
    public function create()
    {
        return view('programacion.create');
    }

    /**
     * Guarda un nuevo plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'anio' => 'required|integer|min:2024|max:2030',
            'nombre' => 'required|string|max:255',
        ]);

        $plan = FinPlan::create([
            'anio' => $validated['anio'],
            'version' => 1,
            'nombre' => $validated['nombre'],
            'estado' => 'borrador',
        ]);

        return redirect()->route('programacion.planes.show', $plan->id)
            ->with('success', 'Plan creado exitosamente en estado borrador.');
    }

    /**
     * Aprueba un plan presupuestario.
     */
    public function aprobar($id)
    {
        try {
            $this->planningService->approvePlan($id, Auth::id());
            return redirect()->back()->with('success', 'Plan aprobado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al aprobar el plan: ' . $e->getMessage());
        }
    }

    /**
     * Crea una nueva versión del plan.
     */
    public function versionar($id)
    {
        try {
            $oldPlan = FinPlan::findOrFail($id);
            $newPlan = $this->planningService->createNewVersion($oldPlan->anio, $oldPlan->version);
            return redirect()->route('programacion.planes.show', $newPlan->id)
                ->with('success', "Nueva versión (v{$newPlan->version}) creada exitosamente.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al versionar el plan: ' . $e->getMessage());
        }
    }
}
