<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ImportRun;
use App\Models\StagingFinEjecucion;
use App\Jobs\ValidateNormalizeJob;
use App\Jobs\UpsertFinalJob;

// We find all import runs that have staging execution data
$runIds = StagingFinEjecucion::distinct()->pluck('import_run_id');
$runs = ImportRun::whereIn('id', $runIds)->get();

echo "Found " . $runs->count() . " runs to reprocess.\n";

foreach ($runs as $run) {
    echo "Processing run {$run->id} ({$run->file_name})...\n";
    try {
        // Direct call to handle to avoid queue issues and see errors immediately
        $job1 = new ValidateNormalizeJob($run);
        $job1->handle();

        $job2 = new UpsertFinalJob($run);
        $job2->handle();

        echo "Successfully reprocessed run {$run->id}\n";
    } catch (\Exception $e) {
        echo "Error processing run {$run->id}: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
}
