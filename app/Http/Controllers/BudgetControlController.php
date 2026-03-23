<?php

namespace App\Http\Controllers;

use App\Models\FinPlan;
use App\Models\FinPlanItem;
use App\Models\FinPlanMensual;
use App\Models\FinEjecucionFact;
use App\Models\FinCentroCosto;
use App\Models\FinClasificadorItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetControlController extends Controller
{
    public function index(Request $request)
    {
        $anio = $request->get('anio', date('Y'));
        $mesCorte = (int)$request->get('mes', date('n'));
        $centroCostoId = $request->get('centro_costo_id');
        
        // 1. Obtener Planes para el selector
        $planes = FinPlan::where('anio', $anio)
            ->orderBy('version', 'desc')
            ->get();

        // 2. Identificar Versión a mostrar (Default: Activa)
        $planId = $request->get('plan_id');
        if (!$planId) {
            $planActivo = $planes->where('estado', 'aprobado')->first();
            $planId = $planActivo ? $planActivo->id : ($planes->first() ? $planes->first()->id : null);
        }

        $selectedPlan = $planId ? FinPlan::find($planId) : null;
        $centrosCosto = FinCentroCosto::where('activo', true)->orderBy('nombre')->get();

        if (!$selectedPlan) {
            return view('finance.control.index', [
                'planes' => $planes,
                'centrosCosto' => $centrosCosto,
                'error' => 'No se encontró un plan presupuesto para el año seleccionado.'
            ]);
        }

        // 3. Carga de Datos de Programación (Acumulado hasta mes de corte)
        $programacionQuery = DB::table('fin_plan_items as pi')
            ->join('fin_clasificador_items as ci', 'pi.clasificador_item_id', '=', 'ci.id')
            ->leftJoin('fin_plan_mensual as pm', function($join) use ($mesCorte) {
                $join->on('pm.plan_item_id', '=', 'pi.id')
                     ->where('pm.mes', '<=', $mesCorte);
            })
            ->where('pi.plan_id', $selectedPlan->id)
            ->select(
                'ci.codigo',
                'ci.id as clasificador_id',
                'pi.monto_anual',
                DB::raw('SUM(pm.monto_planificado) as programado_acumulado')
            )
            ->groupBy('ci.codigo', 'ci.id', 'pi.monto_anual');

        if ($centroCostoId) {
            $programacionQuery->where('pi.centro_costo_id', $centroCostoId);
        }

        $programadoData = $programacionQuery->get();

        // 4. Carga de Datos de Ejecución Real (Acumulado hasta mes de corte)
        $ejecucionQuery = FinEjecucionFact::where('anio', $anio)
            ->where('mes', '<=', $mesCorte);

        if ($centroCostoId) {
            $ejecucionQuery->where('centro_costo_id', $centroCostoId);
        }

        $ejecutadoData = $ejecucionQuery->select(
                'codigo_completo',
                DB::raw('SUM(devengado) as ejecutado_acumulado')
            )
            ->groupBy('codigo_completo')
            ->get();
        // 5. Consolidación Jerárquica Total
        // Necesitamos todos los clasificadores del año para asegurar que los padres existan
        $allItems = FinClasificadorItem::where('anio_vigencia', $anio)
            ->where('activo', true)
            ->get();

        $consolidado = [];
        $idToCode = [];
        foreach ($allItems as $ci) {
            $code = $ci->codigo;
            $idToCode[$ci->id] = $code;
            $consolidado[$code] = [
                'id' => $ci->id,
                'denominacion' => $ci->denominacion,
                'parent_id' => $ci->parent_id,
                'nivel' => $ci->nivel,
                'programado_anual' => 0,
                'programado_acumulado' => 0,
                'ejecutado_acumulado' => 0
            ];
        }

        // Función auxiliar para sumar hacia arriba
        $sumUp = function($code, $montoAnual, $montoAcum, $ejecAcum) use (&$consolidado, $idToCode) {
            $currCode = $code;
            while ($currCode && isset($consolidado[$currCode])) {
                $consolidado[$currCode]['programado_anual'] += $montoAnual;
                $consolidado[$currCode]['programado_acumulado'] += $montoAcum;
                $consolidado[$currCode]['ejecutado_acumulado'] += $ejecAcum;
                
                $parentId = $consolidado[$currCode]['parent_id'];
                $currCode = $parentId ? ($idToCode[$parentId] ?? null) : null;
            }
        };

        // Mapear Programación
        foreach ($programadoData as $prog) {
            $sumUp($prog->codigo, $prog->monto_anual, $prog->programado_acumulado, 0);
        }

        // Mapear Ejecución
        foreach ($ejecutadoData as $ejec) {
            $sumUp($ejec->codigo_completo, 0, 0, $ejec->ejecutado_acumulado);
        }

        // 6. Métricas Top (Cards) - Usamos el nivel 1 para el total global
        $metrics = [
            'anual_total' => 0,
            'progr_acum' => 0,
            'exec_acum' => 0,
        ];

        foreach($consolidado as $c) {
            if ($c['nivel'] == 1) {
                $metrics['anual_total'] += $c['programado_anual'];
                $metrics['progr_acum'] += $c['programado_acumulado'];
                $metrics['exec_acum'] += $c['ejecutado_acumulado'];
            }
        }
        
        $metrics['saldo'] = $metrics['anual_total'] - $metrics['exec_acum'];
        $metrics['porcentaje'] = $metrics['progr_acum'] > 0 ? ($metrics['exec_acum'] / $metrics['progr_acum']) * 100 : 0;
        $metrics['desviacion'] = $metrics['exec_acum'] - $metrics['progr_acum'];

        // 7. Preparar Árbol para la Vista
        $tree = [];
        $childCounts = [];
        foreach ($consolidado as $code => $vals) {
            $parentId = $vals['parent_id'];
            if ($parentId) {
                $childCounts[$parentId] = ($childCounts[$parentId] ?? 0) + 1;
            }
        }

        foreach ($consolidado as $code => $vals) {
            $node = (object) array_merge(['codigo' => $code], $vals);
            $node->children_count = $childCounts[$vals['id']] ?? 0;
            $tree[] = $node;
        }
        
        // Ordenar árbol por código
        usort($tree, function($a, $b) { return strcmp($a->codigo, $b->codigo); });

        return view('finance.control.index', compact(
            'planes', 
            'selectedPlan', 
            'centrosCosto', 
            'anio', 
            'mesCorte', 
            'metrics', 
            'tree',
            'centroCostoId'
        ));
    }

    // El método buildHierarchicalTable ya no es necesario con esta lógica integrada
    private function buildHierarchicalTable($anio, $data)
    {
        return [];
    }
}
