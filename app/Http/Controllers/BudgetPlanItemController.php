<?php

namespace App\Http\Controllers;

use App\Models\FinPlan;
use App\Models\FinPlanItem;
use App\Models\FinCentroCosto;
use App\Models\FinClasificadorItem;
use Illuminate\Http\Request;

class BudgetPlanItemController extends Controller
{
    public function create($planId)
    {
        $plan = FinPlan::findOrFail($planId);

        if ($plan->estado !== 'borrador') {
            return redirect()->route('programacion.planes.show', $planId)
                ->with('error', 'Solo se pueden agregar ítems a planes en estado borrador.');
        }

        $centrosCosto = FinCentroCosto::where('activo', true)->orderBy('nombre')->get();

        // Obtenemos clasificadores vigentes para el año del plan
        // IMPORTANTE: Solo permitimos programar en nodos hoja (leaf nodes)
        $clasificadores = FinClasificadorItem::where('anio_vigencia', $plan->anio)
            ->where('activo', true)
            ->whereNotExists(function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('fin_clasificador_items as children')
                    ->whereColumn('children.parent_id', 'fin_clasificador_items.id');
            })
            ->orderBy('codigo')
            ->get();

        return view('programacion.items.create', compact('plan', 'centrosCosto', 'clasificadores'));
    }

    public function store(Request $request, $planId)
    {
        $plan = FinPlan::findOrFail($planId);

        if ($plan->estado !== 'borrador') {
            return redirect()->route('programacion.planes.show', $planId)
                ->with('error', 'Solo se pueden editar planes en estado borrador.');
        }

        $validated = $request->validate([
            'clasificador_item_id' => 'required|exists:fin_clasificador_items,id',
            'centro_costo_id' => 'required|exists:fin_centros_costo,id',
            'monto_anual' => 'required|numeric|min:0',
        ]);

        // Validar que el clasificador sea un nodo hoja
        $isLeaf = !FinClasificadorItem::where('parent_id', $validated['clasificador_item_id'])->exists();
        if (!$isLeaf) {
            return redirect()->back()->withInput()
                ->with('error', 'No se puede programar en un nivel superior. Por favor seleccione un ítem de detalle (nivel operativo).');
        }

        // Evitar duplicados (Plan, Clasificador, CC)
        $exists = FinPlanItem::where('plan_id', $planId)
            ->where('clasificador_item_id', $validated['clasificador_item_id'])
            ->where('centro_costo_id', $validated['centro_costo_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()
                ->with('error', 'Ya existe una asignación para este Clasificador y Centro de Costo en este plan.');
        }

        FinPlanItem::create([
            'plan_id' => $planId,
            'clasificador_item_id' => $validated['clasificador_item_id'],
            'centro_costo_id' => $validated['centro_costo_id'],
            'monto_anual' => $validated['monto_anual'],
        ]);

        return redirect()->route('programacion.planes.show', $planId)
            ->with('success', 'Ítem asignado exitosamente.');
    }

    public function distribuir($itemId)
    {
        $item = FinPlanItem::with(['plan', 'clasificadorItem', 'centroCosto', 'mensualizaciones'])->findOrFail($itemId);

        if ($item->plan->estado !== 'borrador') {
            return redirect()->route('programacion.planes.show', $item->plan_id)
                ->with('error', 'Solo se puede distribuir el presupuesto en planes en estado borrador.');
        }

        // Aseguramos que existan los 12 meses en la colección para la vista
        $mensualizaciones = [];
        for ($i = 1; $i <= 12; $i++) {
            $m = $item->mensualizaciones->where('mes', $i)->first();
            $mensualizaciones[$i] = $m ? $m->monto_planificado : 0;
        }

        return view('programacion.items.distribuir', compact('item', 'mensualizaciones'));
    }

    public function saveDistribuir(Request $request, $itemId)
    {
        $item = FinPlanItem::with('plan')->findOrFail($itemId);

        if ($item->plan->estado !== 'borrador') {
            return redirect()->route('programacion.planes.show', $item->plan_id)
                ->with('error', 'No se puede editar la distribución de un plan aprobado.');
        }

        $validated = $request->validate([
            'meses' => 'required|array|size:12',
            'meses.*' => 'required|numeric|min:0',
        ]);

        $totalMensual = array_sum($validated['meses']);

        // Validación de cuadratura (con margen por redondeo si fuera necesario)
        if (abs($totalMensual - $item->monto_anual) > 0.01) {
            return redirect()->back()->withInput()
                ->with('error', "La suma de los meses ($" . number_format($totalMensual, 0) . ") debe ser igual al monto anual ($" . number_format($item->monto_anual, 0) . ").");
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($item, $validated) {
            // Limpiamos anteriores para este ítem
            $item->mensualizaciones()->delete();

            foreach ($validated['meses'] as $mes => $monto) {
                \App\Models\FinPlanMensual::create([
                    'plan_item_id' => $item->id,
                    'mes' => $mes,
                    'monto_planificado' => $monto,
                ]);
            }
        });

        return redirect()->route('programacion.planes.show', $item->plan_id)
            ->with('success', 'Distribución mensual guardada exitosamente.');
    }
}
