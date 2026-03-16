<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
ini_set('memory_limit', '1024M');
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportRun;
use App\Models\DatasetVersion;
use App\Jobs\GrdStageImportJob;
use App\Jobs\GrdValidateNormalizeJob;
use App\Jobs\GrdUpsertFinalJob;

$version = DatasetVersion::whereHas('dataset', fn($q) => $q->where('slug', 'grd_egresos'))->first();

if (!$version) {
    die("Error: Dataset version for GRD not found.\n");
}

$importRun = ImportRun::create([
    'dataset_version_id' => $version->id,
    'user_id' => 1,
    'file_name' => 'imports/grd/test_grd.xlsx', // Full file already copied here
    'target_anio' => 2025,
    'target_mes' => 1,
    'status' => 'pending',
]);

echo "Created Full Import ID: " . $importRun->id . "\n";

try {
    echo "Starting Full ETL Process (66k rows)...\n";
    
    echo "1. Staging...\n";
    $stage = new GrdStageImportJob($importRun);
    $stage->handle();
    echo "Staging Done.\n";

    echo "2. Validating & Normalizing (including code parsing)...\n";
    $validate = new GrdValidateNormalizeJob($importRun);
    $validate->handle();
    echo "Validation Done.\n";

    echo "3. Upserting to Fact Table...\n";
    $upsert = new GrdUpsertFinalJob($importRun);
    $upsert->handle();
    echo "Upsert Done.\n";

    echo "\nFULL LOAD COMPLETED SUCCESSFULLY!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
