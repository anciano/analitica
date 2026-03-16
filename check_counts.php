<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportRun;
use App\Models\StagingGrdEgreso;
use App\Models\GrdEgresoFact;

$import = ImportRun::latest()->first();
if ($import) {
    echo "Latest Import ID: " . $import->id . "\n";
    echo "Status: " . $import->status . "\n";
    echo "Total rows (from import_run): " . $import->total_rows . "\n";
    echo "Valid rows (from import_run): " . $import->valid_rows . "\n";
}

echo "Staging count: " . StagingGrdEgreso::count() . "\n";
echo "Fact count: " . GrdEgresoFact::count() . "\n";

$first = GrdEgresoFact::first();
if ($first) {
    echo "Sample data (Anio/Mes): " . $first->anio . "/" . $first->mes . "\n";
    echo "Sample Episodio: " . $first->episodio_cmbd . "\n";
}
