<?php

namespace App\Jobs;

use App\Models\ImportRun;
use App\Models\StagingFinEjecucion;
use App\Models\FinEjecucionFact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpsertFinalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ImportRun $importRun)
    {
    }

    public function handle(): void
    {
        $this->importRun->update(['status' => 'upserting']);

        DB::transaction(function () {
            $validStaging = StagingFinEjecucion::where('import_run_id', $this->importRun->id)
                ->where('is_valid', true)
                ->get();

            foreach ($validStaging as $row) {
                $data = $row->payload_parsed;

                FinEjecucionFact::upsert([
                    array_merge($data, [
                        'anio' => $this->importRun->target_anio,
                        'mes' => $this->importRun->target_mes,
                        'import_run_id' => $this->importRun->id,
                        'fuente' => 'ejecucion_presupuestaria',
                    ])
                ], [
                    'anio',
                    'mes',
                    'codigo_completo',
                    'fuente'
                ], [
                    'concepto',
                    'presupuesto_vigente',
                    'compromiso',
                    'devengado',
                    'pagado',
                    'saldo',
                    'subtitulo',
                    'item',
                    'asignacion',
                    'nivel',
                    'codigo_completo',
                    'requerimiento',
                    'deuda_flotante',
                    'saldo_por_aplicar',
                    'saldo_por_devengar',
                    'row_number',
                    'import_run_id'
                ]);
            }

            $this->importRun->update([
                'status' => 'completed',
            ]);
        });
    }
}
