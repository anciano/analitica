<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
use App\Models\FinClasificadorItem;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Merging and Repairing hierarchy...\n";

DB::transaction(function () {
    // 1. Identify duplicates
    $items = FinClasificadorItem::all();
    $seen = [];
    $toMerge = [];

    foreach ($items as $item) {
        $cleanCode = preg_replace('/[^0-9]/', '', $item->codigo);
        $key = $cleanCode . '|' . $item->anio_vigencia;

        if (isset($seen[$key])) {
            $toMerge[$item->id] = $seen[$key];
        } else {
            $seen[$key] = $item->id;
        }
    }

    // 2. Update references and delete duplicates
    foreach ($toMerge as $dupId => $originalId) {
        echo "Merging ID $dupId into $originalId...\n";

        DB::table('fin_plan_items')->where('clasificador_item_id', $dupId)->update(['clasificador_item_id' => $originalId]);
        DB::table('fin_cc_item_imputacion')->where('clasificador_item_id', $dupId)->update(['clasificador_item_id' => $originalId]);

        DB::table('fin_clasificador_items')->where('id', $dupId)->delete();
    }

    // 3. Clean remaining codes
    $remainingItems = FinClasificadorItem::all();
    foreach ($remainingItems as $item) {
        $cleanCode = preg_replace('/[^0-9]/', '', $item->codigo);
        if ($item->codigo !== $cleanCode) {
            $item->codigo = $cleanCode;
            $item->save();
        }
    }

    // 4. Assign Nivel
    foreach ($remainingItems as $item) {
        $len = strlen($item->codigo);
        if ($len <= 2)
            $item->nivel = 1;
        elseif ($len <= 4)
            $item->nivel = 2;
        elseif ($len <= 7)
            $item->nivel = 3;
        elseif ($len <= 10)
            $item->nivel = 4;
        else
            $item->nivel = 5;
        $item->save();
    }

    // 5. Assign Parent ID
    $allItems = FinClasificadorItem::all()->keyBy('codigo');
    foreach ($allItems as $codigo => $item) {
        $parentCode = null;
        if ($item->nivel == 2)
            $parentCode = substr($codigo, 0, 2);
        elseif ($item->nivel == 3)
            $parentCode = substr($codigo, 0, 4);
        elseif ($item->nivel == 4)
            $parentCode = substr($codigo, 0, 7);
        elseif ($item->nivel == 5)
            $parentCode = substr($codigo, 0, 10);

        if ($parentCode && isset($allItems[$parentCode])) {
            $item->parent_id = $allItems[$parentCode]->id;
            $item->save();
        } else {
            $item->parent_id = null;
            $item->save();
        }
    }
});

echo "Hierarchy repaired successfully.\n";

$stats = FinClasificadorItem::select('nivel', DB::raw('count(*) as count'))
    ->groupBy('nivel')
    ->orderBy('nivel')
    ->get();

foreach ($stats as $s) {
    echo "Nivel {$s->nivel}: {$s->count} items\n";
}

$withParent = FinClasificadorItem::whereNotNull('parent_id')->count();
echo "Total with Parent: $withParent / " . FinClasificadorItem::count() . "\n";
