<?php

use App\Models\ImportRun;
use App\Models\DatasetVersion;
use App\Jobs\GrdStageImportJob;
use Illuminate\Support\Facades\Artisan;

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$version = DatasetVersion::whereHas('dataset', function($q) {
    $q->where('slug', 'grd_egresos');
})->first();

if (!$version) {
    die("Error: Dataset version for GRD not found.\n");
}

$importRun = ImportRun::create([
    'dataset_version_id' => $version->id,
    'user_id' => 1,
    'file_name' => 'imports/grd/test_grd.xlsx',
    'target_anio' => 2025,
    'target_mes' => 5,
    'status' => 'pending',
]);

echo "Created ImportRun #{$importRun->id}\n";
echo "Dispatching GrdStageImportJob...\n";

GrdStageImportJob::dispatch($importRun);

echo "Job dispatched. Run 'php artisan queue:work' to process.\n";
