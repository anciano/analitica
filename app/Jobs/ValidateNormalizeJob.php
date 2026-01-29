<?php

namespace App\Jobs;

use App\Models\ImportRun;
use App\Models\StagingFinEjecucion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ValidateNormalizeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ImportRun $importRun)
    {
    }

    public function handle(): void
    {
        $this->importRun->update(['status' => 'validating']);

        $stagingRows = StagingFinEjecucion::where('import_run_id', $this->importRun->id)->get();

        $validCount = 0;
        $errorCount = 0;

        foreach ($stagingRows as $row) {
            $payload = $row->payload_raw;
            $normalized = [];
            $errors = [];

            // 1 & 2. Handle composite 'Concepto Presupuestario' or separate subtitulo/item
            $conceptoCol = $this->getVal($payload, 'concepto presupuestario');
            if ($conceptoCol) {
                // Format: "21 GASTOS EN PERSONAL" or "2101 Personal de Planta"
                $parts = explode(' ', trim($conceptoCol), 2);
                $code = $parts[0] ?? '';
                if (strlen($code) == 2) {
                    $normalized['subtitulo'] = $code;
                    $normalized['item'] = '00';
                } elseif (strlen($code) == 4) {
                    $normalized['subtitulo'] = substr($code, 0, 2);
                    $normalized['item'] = substr($code, 2, 2);
                } else {
                    $normalized['subtitulo'] = '00';
                    $normalized['item'] = '00';
                }
            } else {
                $normalized['subtitulo'] = trim($this->getVal($payload, 'subtitulo') ?? '00');
                $normalized['item'] = trim($this->getVal($payload, 'item') ?? '00');
            }

            // 3. Required & Numeric: devengado
            $devengadoRaw = $this->getVal($payload, 'devengado');
            $devengado = $this->normalizeNumeric($devengadoRaw);
            if ($devengado === null) {
                $errors[] = ['column' => 'devengado', 'message' => 'Estructura numérica inválida', 'original' => $devengadoRaw];
            } else {
                $normalized['devengado'] = $devengado;
            }

            // 4. Other fields (optional but normalized)
            $normalized['asignacion'] = trim($this->getVal($payload, 'asignacion') ?? '');
            $normalized['concepto'] = trim($this->getVal($payload, 'concepto') ?? '');
            $normalized['presupuesto_vigente'] = $this->normalizeNumeric($this->getVal($payload, 'presupuesto_vigente'));
            $normalized['compromiso'] = $this->normalizeNumeric($this->getVal($payload, 'compromiso'));
            $normalized['pagado'] = $this->normalizeNumeric($this->getVal($payload, 'pagado'));
            $normalized['saldo'] = $this->normalizeNumeric($this->getVal($payload, 'saldo'));

            $isValid = empty($errors);
            $row->update([
                'payload_parsed' => $normalized,
                'is_valid' => $isValid,
                'validation_errors' => $errors,
            ]);

            if ($isValid)
                $validCount++;
            else
                $errorCount++;
        }

        $this->importRun->update([
            'valid_rows' => $validCount,
            'error_rows' => $errorCount,
        ]);

        UpsertFinalJob::dispatch($this->importRun);
    }

    protected function getVal(array $payload, string $key): mixed
    {
        // Try exact match or contains (for flexible mapping)
        if (isset($payload[$key]))
            return $payload[$key];
        foreach ($payload as $pKey => $val) {
            if (str_contains(strtolower($pKey), strtolower($key)))
                return $val;
        }
        return null;
    }

    protected function normalizeNumeric(mixed $value): ?float
    {
        if ($value === null || $value === '')
            return 0.0;
        if (is_numeric($value))
            return (float) $value;

        // Strip currency, spaces (except commas/dots)
        $clean = preg_replace('/[^\d,.\-\(\)]/', '', (string) $value);

        // If it's a "point as thousands" format (e.g., 50.641.944.925)
        if (substr_count($clean, '.') > 1) {
            $clean = str_replace('.', '', $clean);
        }
        // If it's a "comma as thousands" format (e.g., 50,641,944,925)
        if (substr_count($clean, ',') > 1) {
            $clean = str_replace(',', '', $clean);
        }

        // Handle parentheses as negative
        $isNegative = false;
        if (preg_match('/\((.*)\)/', $clean, $matches)) {
            $clean = $matches[1];
            $isNegative = true;
        }

        // Standardize European vs American formats
        // (This is a naive heuristic: if both , and . exist, assume last is decimal)
        if (str_contains($clean, ',') && str_contains($clean, '.')) {
            if (strrpos($clean, ',') > strrpos($clean, '.')) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '', $clean);
            }
        } elseif (str_contains($clean, ',')) {
            // Assume single comma is decimal separator (typical in Chile)
            $clean = str_replace(',', '.', $clean);
        }

        if (!is_numeric($clean))
            return null;

        $result = (float) $clean;
        return $isNegative ? -$result : $result;
    }
}
