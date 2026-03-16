<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportRun;
use App\Jobs\GrdStageImportJob;
use App\Jobs\GrdValidateNormalizeJob;
use App\Jobs\GrdUpsertFinalJob;

$import = ImportRun::latest()->first();

if (!$import) {
    die("No import found.\n");
}

echo "Processing Import ID: " . $import->id . "\n";

try {
    echo "Running Stage Job...\n";
    $stage = new GrdStageImportJob($import);
    $stage->handle();
    echo "Stage Job Done.\n";

    echo "Running Validate Job...\n";
    $validate = new GrdValidateNormalizeJob($import);
    $validate->handle();
    echo "Validate Job Done.\n";

    echo "Running Upsert Job...\n";
    $upsert = new GrdUpsertFinalJob($import);
    $upsert->handle();
    echo "Upsert Job Done.\n";

    echo "ETL Process Finished successfully.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
