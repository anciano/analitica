<?php

use App\Models\FinEjecucionFact;
use App\Models\ImportRun;
use App\Models\StagingFinEjecucion;
use App\Jobs\StageImportJob;

echo "Limpiando datos de Abril 2025...\n";

// 1. Find April ImportRun(s)
$runs = ImportRun::where('target_anio', 2025)->where('target_mes', 4)->get();
$runIds = $runs->pluck('id');

// 2. Delete Staging
if ($runIds->isNotEmpty()) {
    StagingFinEjecucion::whereIn('import_run_id', $runIds)->delete();
}

// 3. Delete Facts
FinEjecucionFact::where('anio', 2025)->where('mes', 4)->delete();

// 4. Delete ImportRun
ImportRun::whereIn('id', $runIds)->delete();

echo "Re-importando archivo...\n";

// 5. Create new ImportRun
$run = ImportRun::create([
    'file_path' => 'public/GASTOS ABRIL 2025.xlsx',
    'file_name' => 'public/GASTOS ABRIL 2025.xlsx',
    'status' => 'pending',
    'user_id' => 1,
    'dataset_version_id' => 1, // Assuming version 1
    'target_anio' => 2025,
    'target_mes' => 4,
]);

// 6. Dispatch Job
dispatch(new StageImportJob($run));

echo "Job despachado para ImportRun #{$run->id}\n";
