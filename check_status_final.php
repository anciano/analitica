<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportRun;
use App\Models\StagingGrdEgreso;
use App\Models\GrdEgresoFact;

$id = 37;
$import = ImportRun::find($id);

if (!$import) {
    die("Import $id not found.\n");
}

echo "Import ID: " . $import->id . "\n";
echo "Status: " . $import->status . "\n";

$stgCount = StagingGrdEgreso::where('import_run_id', $id)->count();
$validStgCount = StagingGrdEgreso::where('import_run_id', $id)->where('is_valid', true)->count();
$factCount = GrdEgresoFact::where('import_run_id', $id)->count();

echo "Staging Count: $stgCount\n";
echo "Valid Staging Count: $validStgCount\n";
echo "Fact Count: $factCount\n";

if ($factCount == 0 && $validStgCount > 0) {
    echo "WARNING: Valid staging exists but Fact is empty. Possible transaction rollback or failure.\n";
    // Check for any errors in the first few rows
    $firstValid = StagingGrdEgreso::where('import_run_id', $id)->where('is_valid', true)->first();
    echo "First Valid Row Data: " . json_encode($firstValid->payload_parsed) . "\n";
}
