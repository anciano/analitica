<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GrdEgresoFact;

$count = GrdEgresoFact::count();
echo "Total Rows: $count\n";

$sample = GrdEgresoFact::whereNotNull('dx_secundarios')->first();
if ($sample) {
    echo "Sample dx_secundarios (raw): " . json_encode($sample->getRawOriginal('dx_secundarios')) . "\n";
    echo "Sample dx_secundarios (parsed): " . json_encode($sample->dx_secundarios) . "\n";
}

$sampleProc = GrdEgresoFact::whereNotNull('proc_secundarios')->first();
if ($sampleProc) {
    echo "Sample proc_secundarios (parsed): " . json_encode($sampleProc->proc_secundarios) . "\n";
}
