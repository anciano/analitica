<?php

use App\Models\FinPlan;

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$planes = FinPlan::where('anio', 2026)->orderBy('version')->get();
foreach ($planes as $p) {
    echo "ID: " . $p->id . " | Version: " . $p->version . " | Estado: " . $p->estado . " | Nombre: " . $p->nombre . "\n";
}
