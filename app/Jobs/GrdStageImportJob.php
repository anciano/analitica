<?php

namespace App\Jobs;

use App\Models\ImportRun;
use App\Models\StagingGrdEgreso;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class GrdReadFilter implements IReadFilter
{
    private $startRow = 1;
    private $endRow = 100;

    public function setRows($startRow, $endRow) {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool {
        if ($row == 1 || ($row >= $this->startRow && $row <= $this->endRow)) {
            return true;
        }
        return false;
    }
}

class GrdStageImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes

    public function __construct(protected ImportRun $importRun)
    {
    }

    public function handle(): void
    {
        $this->importRun->update(['status' => 'processing']);
        ini_set('memory_limit', '1024M');

        try {
            $filePath = storage_path('app/private/' . $this->importRun->file_name);
            \Illuminate\Support\Facades\Log::info("GRD Path: " . $filePath);

            if (!file_exists($filePath)) {
                throw new \Exception("File not found: " . $filePath);
            }

            $reader = IOFactory::createReaderForFile($filePath);
            \Illuminate\Support\Facades\Log::info("GRD Reader created");
            $reader->setReadDataOnly(true);
            
            $filter = new GrdReadFilter();
            // We read the whole file now, so no limit here
            $filter->setRows(1, 100000); 
            $reader->setReadFilter($filter);

            \Illuminate\Support\Facades\Log::info("GRD Loading spreadsheet iterator...");
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $headers = [];
            $totalRows = 0;
            $batch = [];

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[$cell->getColumn()] = $cell->getValue();
                }

                if ($row->getRowIndex() === 1) {
                    foreach ($rowData as $col => $val) {
                        $headers[$col] = strtolower(trim((string)$val));
                    }
                    continue;
                }

                if (empty(array_filter($rowData))) continue;

                $payload = [];
                foreach ($headers as $col => $name) {
                    $payload[$name] = $rowData[$col] ?? null;
                }

                $batch[] = [
                    'import_run_id' => $this->importRun->id,
                    'row_number' => $row->getRowIndex(),
                    'payload_raw' => json_encode($payload),
                    'is_valid' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($batch) >= 1000) {
                    \App\Models\StagingGrdEgreso::insert($batch);
                    $batch = [];
                }

                $totalRows++;
            }

            if (!empty($batch)) {
                \App\Models\StagingGrdEgreso::insert($batch);
            }

            $this->importRun->update([
                'total_rows' => $totalRows,
                'status' => 'validating'
            ]);

            \Illuminate\Support\Facades\Log::info("GRD Staging finished. Total: $totalRows rows.");

            GrdValidateNormalizeJob::dispatch($this->importRun);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("GRD Staging failed: " . $e->getMessage());
            $this->importRun->update(['status' => 'failed']);
        }
    }
}
