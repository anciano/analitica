<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
use App\Models\FinClasificadorItem;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = FinClasificadorItem::orderBy('codigo')->limit(20)->get();

foreach ($items as $item) {
    echo "ID: {$item->id} | Codigo: {$item->codigo} | Nivel: {$item->nivel} | Parent: " . ($item->parent_id ?? 'NULL') . "\n";
}

$hasParents = FinClasificadorItem::whereNotNull('parent_id')->count();
echo "\nTotal items with parent_id: $hasParents\n";

$leafNodes = FinClasificadorItem::whereNotExists(function ($query) {
    $query->select(DB::raw(1))
        ->from('fin_clasificador_items as children')
        ->whereColumn('children.parent_id', 'fin_clasificador_items.id');
})->count();

echo "Total leaf nodes: $leafNodes\n";
