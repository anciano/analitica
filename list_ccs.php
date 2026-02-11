<?php

use App\Models\FinCentroCosto;

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ccs = FinCentroCosto::all();
foreach ($ccs as $cc) {
    echo $cc->codigo . " - " . $cc->nombre . "\n";
}
