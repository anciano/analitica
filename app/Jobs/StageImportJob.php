<?php

namespace App\Jobs;

use App\Models\ImportRun;
use App\Models\StagingFinEjecucion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StageImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ImportRun $importRun)
    {
    }

    public function handle(): void
    {
        $this->importRun->update(['status' => 'processing']);

        try {
            // Handle disk root correctly (private vs public)
            if (str_starts_with($this->importRun->file_name, 'public/')) {
                $realPath = str_replace('public/', '', $this->importRun->file_name);
                $filePath = Storage::disk('public')->path($realPath);
            } else {
                $filePath = Storage::disk('local')->path($this->importRun->file_name);
            }

            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $headerRowIndex = $this->findHeaderRow($rows);
            if ($headerRowIndex === -1) {
                throw new \Exception("Could not find header row");
            }

            $headers = array_map(function ($h) {
                return strtolower(trim($h));
            }, $rows[$headerRowIndex]);
            $dataRows = array_slice($rows, $headerRowIndex + 1);

            $totalRows = 0;
            foreach ($dataRows as $index => $row) {
                if ($this->shouldIgnoreRow($row)) {
                    continue;
                }

                StagingFinEjecucion::create([
                    'import_run_id' => $this->importRun->id,
                    'row_number' => $headerRowIndex + $index + 2,
                    'payload_raw' => array_combine($headers, $row),
                    'is_valid' => false,
                ]);
                $totalRows++;
            }

            $this->importRun->update([
                'total_rows' => $totalRows,
            ]);

            ValidateNormalizeJob::dispatch($this->importRun);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Staging failed: " . $e->getMessage());
            $this->importRun->update(['status' => 'failed']);
            // Record error logic
        }
    }

    protected function findHeaderRow(array $rows): int
    {
        $keywords = ['subtitulo', 'item', 'devengado', 'concepto', 'presupuesto'];
        foreach ($rows as $index => $row) {
            $rowString = strtolower(implode(' ', array_filter($row)));
            $matches = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($rowString, $keyword)) {
                    $matches++;
                }
            }
            if ($matches >= 2)
                return $index;
        }
        return -1;
    }

    protected function shouldIgnoreRow(array $row): bool
    {
        $rowString = strtoupper(implode(' ', array_filter($row)));
        if (str_contains($rowString, 'TOTAL'))
            return true;
        if (empty(array_filter($row)))
            return true;
        return false;
    }
}
