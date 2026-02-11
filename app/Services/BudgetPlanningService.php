<?php

namespace App\Services;

use App\Models\FinPlan;
use App\Models\FinPlanItem;
use App\Models\FinPlanMensual;
use Illuminate\Support\Facades\DB;
use Exception;

class BudgetPlanningService
{
    /**
     * Crea una nueva versión de un plan a partir de uno existente.
     */
    public function createNewVersion(int $anio, int $fromVersion): FinPlan
    {
        return DB::transaction(function () use ($anio, $fromVersion) {
            $basePlan = FinPlan::where('anio', $anio)->where('version', $fromVersion)->firstOrFail();

            // 0. Validar si ya existe un borrador para este año
            $existingDraft = FinPlan::where('anio', $anio)->where('estado', 'borrador')->first();
            if ($existingDraft) {
                throw new Exception("Ya existe una versión en estado 'borrador' para el año {$anio} (v{$existingDraft->version}). Debe aprobarla o eliminarla antes de crear una nueva.");
            }

            // 1. Calcular siguiente versión basándose en el máximo actual
            $maxVersion = FinPlan::where('anio', $anio)->max('version');

            $newVersion = $basePlan->replicate();
            $newVersion->version = $maxVersion + 1;
            $newVersion->estado = 'borrador';
            $newVersion->aprobado_at = null;
            $newVersion->aprobado_by = null;
            $newVersion->save();

            // 2. Replicar ítems
            foreach ($basePlan->items as $item) {
                $newItem = $item->replicate();
                $newItem->plan_id = $newVersion->id;
                $newItem->save();

                // 3. Replicar mensualización
                foreach ($item->mensualizaciones as $mensual) {
                    $newMensual = $mensual->replicate();
                    $newMensual->plan_item_id = $newItem->id;
                    $newMensual->save();
                }
            }

            return $newVersion;
        });
    }

    /**
     * Valida que la suma mensual coincida con la anual para todos los ítems del plan.
     */
    public function validatePlanTotals(int $planId): array
    {
        $errors = [];
        $items = FinPlanItem::with('mensualizaciones')->where('plan_id', $planId)->get();

        foreach ($items as $item) {
            $totalMensual = $item->mensualizaciones->sum('monto_planificado');
            if (abs($totalMensual - $item->monto_anual) > 0.01) {
                $errors[] = "El ítem ID {$item->id} tiene una diferencia de " . ($item->monto_anual - $totalMensual) . " entre el anual y la suma mensual.";
            }
        }

        return $errors;
    }

    /**
     * Aprueba un plan y marca el anterior como 'historico'.
     */
    public function approvePlan(int $planId, int $userId): void
    {
        DB::transaction(function () use ($planId, $userId) {
            $plan = FinPlan::findOrFail($planId);

            // Marcar anteriores como histórico
            FinPlan::where('anio', $plan->anio)
                ->where('id', '!=', $planId)
                ->where('estado', 'aprobado')
                ->update(['estado' => 'historico']);

            $plan->update([
                'estado' => 'aprobado',
                'aprobado_at' => now(),
                'aprobado_by' => $userId
            ]);
        });
    }
}
