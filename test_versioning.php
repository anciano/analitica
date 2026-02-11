<?php

use App\Services\BudgetPlanningService;
use App\Models\FinPlan;

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new BudgetPlanningService();

echo "Testing versioning with existing draft...\n";
try {
    // Intentar crear una versión cuando ya existe v2 borrador
    $service->createNewVersion(2026, 1);
    echo "ERROR: Saltó la validación de borrador existente.\n";
} catch (\Exception $e) {
    echo "CORRECTO: " . $e->getMessage() . "\n";
}

echo "\nDeleting v2 draft for clean test...\n";
FinPlan::where('anio', 2026)->where('version', 2)->delete();

echo "Testing versioning creation...\n";
try {
    $newPlan = $service->createNewVersion(2026, 1);
    echo "SUCCESS: Created Version " . $newPlan->version . " (ID: " . $newPlan->id . ")\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\nFinal state for 2026:\n";
$planes = FinPlan::where('anio', 2026)->get();
foreach ($planes as $p) {
    echo "v" . $p->version . " (" . $p->estado . ")\n";
}
