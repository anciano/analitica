<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
use App\Models\FinClasificadorItem;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Repairing hierarchy for FinClasificadorItem with aggressive trimming...\n";

DB::transaction(function () {
    // 1. Clean current data (trim non-digit characters to be safe)
    $items = FinClasificadorItem::all();
    foreach ($items as $item) {
        $cleanCode = preg_replace('/[^0-9]/', '', $item->codigo);
        if ($cleanCode !== $item->codigo) {
            echo "Cleaning code: '{$item->codigo}' -> '{$cleanCode}'\n";
            $item->codigo = $cleanCode;
            $item->save();
        }
    }

    // 2. Assign Nivel based on length
    // L1: 2, L2: 4, L3: 7, L4: 10, L5: 12
    $items = FinClasificadorItem::all();
    foreach ($items as $item) {
        $len = strlen($item->codigo);
        $oldNivel = $item->nivel;
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

        if ($oldNivel != $item->nivel) {
            $item->save();
        }
    }

    // 3. Assign Parent ID
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
