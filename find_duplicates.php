<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
use App\Models\FinClasificadorItem;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Finding duplicates in FinClasificadorItem...\n";

$items = FinClasificadorItem::all();
$seen = [];
$duplicates = [];

foreach ($items as $item) {
    $cleanCode = preg_replace('/[^0-9]/', '', $item->codigo);
    $key = $cleanCode . '|' . $item->anio_vigencia;

    if (isset($seen[$key])) {
        $duplicates[$key][] = $item->id;
    } else {
        $seen[$key] = $item->id;
    }
}

if (empty($duplicates)) {
    echo "No duplicates found with aggressive trimming.\n";
} else {
    echo "Found " . count($duplicates) . " sets of duplicates:\n";
    foreach ($duplicates as $key => $ids) {
        $originalId = $seen[$key];
        echo "Code|Anio: $key -> Original ID: $originalId, Duplicates IDs: " . implode(', ', $ids) . "\n";
    }
}
