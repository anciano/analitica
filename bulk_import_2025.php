<?php

use App\Models\FinEjecucionFact;
use App\Models\ImportRun;
use App\Models\User;
use App\Models\DatasetVersion;
use App\Jobs\StageImportJob;
use Illuminate\Support\Facades\Storage;

$files = [
    1 => 'GASTOS ENERO 2025.xlsx',
    2 => 'GASTOS FEBRERO 2025.xlsx',
    3 => 'GASTOS MARZO 2025.xlsx',
    4 => 'GASTOS ABRIL 2025.xlsx',
    5 => 'GASTOS MAYO 2025.xlsx',
    6 => 'GASTOS JUNIO 2025.xlsx',
    7 => 'GASTOS JULIO 2025.xlsx',
    8 => 'GASTOS AGOSTO 2025.xlsx',
    9 => 'GASTOS SEPTIEMBRE 2025.xlsx',
    10 => 'GASTOS OCTUBRE 2025.xlsx',
    11 => 'GASTOS NOVIEMBRE 2025.xlsx',
    12 => 'GASTOS DICIEMBRE 2025.xlsx',
];

echo "Clearing 2025 data...\n";

// Get IDs to delete
$runIds = ImportRun::where('target_anio', 2025)->pluck('id');

// Delete related Staging records
\App\Models\StagingFinEjecucion::whereIn('import_run_id', $runIds)->delete();

// Delete related Import Errors (if any model exists, skipping for now as not used in script but good practice)
// \App\Models\ImportError::whereIn('import_run_id', $runIds)->delete();

// Now delete Runs and Facts
FinEjecucionFact::where('anio', 2025)->delete();
ImportRun::whereIn('id', $runIds)->delete();

$userId = User::first()->id ?? 1;
$datasetVersionId = DatasetVersion::where('is_active', true)->first()->id ?? 1;

foreach ($files as $month => $filename) {
    if (!Storage::disk('public')->exists($filename)) {
        echo "File not found: $filename\n";
        continue;
    }

    echo "Queueing $filename...\n";

    $importRun = ImportRun::create([
        'dataset_version_id' => $datasetVersionId,
        'user_id' => $userId,
        'file_name' => 'public/' . $filename,
        'target_anio' => 2025,
        'target_mes' => $month,
        'status' => 'pending',
    ]);

    StageImportJob::dispatch($importRun);
}

echo "Done dispatching.\n";
