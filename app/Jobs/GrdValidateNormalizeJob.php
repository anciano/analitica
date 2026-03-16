<?php

namespace App\Jobs;

use App\Models\ImportRun;
use App\Models\StagingGrdEgreso;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GrdValidateNormalizeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function __construct(protected ImportRun $importRun)
    {
    }

    public function handle(): void
    {
        $this->importRun->update(['status' => 'validating']);
        ini_set('memory_limit', '1024M');

        $validCount = 0;
        $errorCount = 0;

        StagingGrdEgreso::where('import_run_id', $this->importRun->id)
            ->chunk(1000, function ($stagingRows) use (&$validCount, &$errorCount) {
                foreach ($stagingRows as $row) {
                    $payload = $row->payload_raw;
                    $normalized = [];

                    // Mapping based on inspection
                    $normalized['anio'] = (int)($payload['año egreso'] ?? 0);
                    $normalized['mes_nombre'] = trim($payload['mes egreso (descripción)'] ?? '');
                    $normalized['mes'] = $this->mapMonth($normalized['mes_nombre']);
                    $normalized['num_historia'] = trim((string)($payload['nº historia'] ?? ''));
                    $normalized['episodio_cmbd'] = trim((string)($payload['episodio cmbd'] ?? ''));
                    $normalized['prevision'] = trim($payload['prevision (desc)'] ?? '');
                    $normalized['sexo'] = trim($payload['sexo (desc)'] ?? '');
                    $normalized['edad'] = (int)($payload['edad'] ?? 0);
                    
                    // GRD Parsing: "081602 - PH OTROS..."
                    $grdRaw = trim($payload['grd'] ?? '');
                    if ($grdRaw) {
                        $parts = explode(' - ', $grdRaw, 2);
                        $normalized['grd_id_original'] = trim($parts[0] ?? '');
                        $normalized['grd_nombre'] = trim($parts[1] ?? '');
                    }

                    $normalized['dx_principal'] = trim($payload['diagnóstico principal'] ?? '');
                    $normalized['dx_secundarios'] = $this->parseClinicalCodes($payload['conjunto dx secundarios'] ?? '');
                    $normalized['proc_principal'] = trim($payload['procedimiento principal'] ?? '');
                    $normalized['proc_secundarios'] = $this->parseClinicalCodes($payload['conjunto procedimientos secundarios'] ?? '');
                    
                    $normalized['estancia_media'] = (float)($payload['estancia media'] ?? 0);
                    $normalized['corte_superior'] = (float)($payload['punto corte superior'] ?? 0);
                    
                    // VM detection from the CASE column or similar
                    $vmVal = strtolower(trim($payload[array_keys($payload)[15]] ?? 'no')); // Column N was the CASE one
                    $normalized['tiene_vm'] = ($vmVal === 'sí' || $vmVal === 'si' || $vmVal === 'yes' || $vmVal === '1');
                    
                    $normalized['peso_grd'] = (float)($payload['peso grd medio (todos)'] ?? 0);
                    $normalized['egresos'] = (int)($payload['egresos'] ?? 1);

                    $isValid = ($normalized['anio'] > 0);

                    /** @var \App\Models\StagingGrdEgreso $row */
                    $row->update([
                        'payload_parsed' => $normalized,
                        'is_valid' => $isValid,
                    ]);

                    if ($isValid) $validCount++;
                    else $errorCount++;
                }
            });

        $this->importRun->update([
            'valid_rows' => $validCount,
            'error_rows' => $errorCount,
        ]);

        GrdUpsertFinalJob::dispatch($this->importRun);
    }

    protected function parseClinicalCodes(string $raw): array
    {
        if (empty($raw)) return [];
        // Matches content between brackets: [89.34] -> 89.34
        preg_match_all('/\[(.*?)\]/', $raw, $matches);
        return $matches[1] ?? [];
    }

    protected function mapMonth(string $name): int
    {
        $months = [
            'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4,
            'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8,
            'septiembre' => 9, 'septiembr' => 9, 'setiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12
        ];
        return $months[strtolower($name)] ?? 0;
    }
}
