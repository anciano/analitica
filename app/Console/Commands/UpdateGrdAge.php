<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class UpdateGrdAgeFilter implements IReadFilter {
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool {
        // We only need the ID and Age columns
        $col = substr($columnAddress, 0, 1);
        // Column D is indices [3], Column Q is indices [16]
        // Actually columnAddress could be multi-letter, but for [3] and [16] it's D and Q.
        return in_array($columnAddress, ['D', 'Q']);
    }
}

class UpdateGrdAge extends Command
{
    protected $signature = 'app:update-grd-age {file}';
    protected $description = 'Update age for GRD records from Excel';

    public function handle()
    {
        $filePath = $this->argument('file');
        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        $this->info("Loading file: $filePath");
        ini_set('memory_limit', '2048M');

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        // We don't use filter here because we want to be safe with column letters vs indices
        
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $this->info("Processing rows...");
        $bar = $this->output->createProgressBar(count($rows) - 1);
        $bar->start();

        $count = 0;
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header

                $episodio = trim((string)($row[3] ?? ''));
                $edad = (int)($row[16] ?? 0);

                if ($episodio && $edad > 0) {
                    DB::table('grd_egresos_fact')
                        ->where('episodio_cmbd', $episodio)
                        ->update(['edad' => $edad]);
                    $count++;
                }

                $bar->advance();
                
                if ($count % 500 === 0) {
                    DB::commit();
                    DB::beginTransaction();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        $bar->finish();
        $this->newLine();
        $this->info("Updated $count records.");
    }
}
