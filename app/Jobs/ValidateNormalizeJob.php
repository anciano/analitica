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

            // 1 & 2. Handle 'Concepto Presupuestario'
            $conceptoCol = $payload['concepto presupuestario'] ?? '';
            $normalized['concepto'] = trim($conceptoCol);

            if ($conceptoCol) {
                $parts = explode(' ', trim($conceptoCol), 2);
                $code = trim($parts[0] ?? '');
                $normalized['codigo_completo'] = $code;

                // Extract levels based on Chilean budget structure
                $normalized['subtitulo'] = (strlen($code) >= 2) ? substr($code, 0, 2) : str_pad($code, 2, '0', STR_PAD_LEFT);
                $normalized['item'] = (strlen($code) >= 4) ? substr($code, 2, 2) : '00';
                $normalized['asignacion'] = (strlen($code) > 4) ? substr($code, 4) : '';
            } else {
                $normalized['codigo_completo'] = '';
                $normalized['subtitulo'] = '00';
                $normalized['item'] = '00';
                $normalized['asignacion'] = '';
            }

            // 3. Normalized Budgetary Fields (Explicit mapping)
            $normalized['presupuesto_vigente'] = $this->normalizeNumeric($payload['ley de presupuestos'] ?? 0);
            $normalized['compromiso'] = $this->normalizeNumeric($payload['compromiso'] ?? 0);
            $normalized['devengado'] = $this->normalizeNumeric($payload['devengado'] ?? 0);
            $normalized['pagado'] = $this->normalizeNumeric($payload['efectivo'] ?? 0);
            $normalized['saldo'] = $this->normalizeNumeric($payload['saldo por comprometer'] ?? 0);

            // New fields
            $normalized['requerimiento'] = $this->normalizeNumeric($payload['requerimiento'] ?? 0);
            $normalized['deuda_flotante'] = $this->normalizeNumeric($payload['deuda flotante'] ?? 0);
            $normalized['saldo_por_aplicar'] = $this->normalizeNumeric($payload['saldo por aplicar'] ?? 0);
            $normalized['saldo_por_devengar'] = $this->normalizeNumeric($payload['saldo por devengar'] ?? 0);

            $normalized['nivel'] = (int) ($payload['nivel'] ?? 0);
            $normalized['row_number'] = $row->row_number;

            $isValid = ($normalized['devengado'] !== null);
            $row->update([
                'payload_parsed' => $normalized,
                'is_valid' => $isValid,
                'validation_errors' => $isValid ? [] : [['column' => 'devengado', 'message' => 'Monto devengado inválido']],
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
