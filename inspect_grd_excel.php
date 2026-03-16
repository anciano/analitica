<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class MyReadFilter implements IReadFilter
{
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        return $row == 1;
    }
}

$filePath = __DIR__ . '/EGRESOS HOSPITALIZARIOS 2018 - 2025 HRC.xlsx';

try {
    $reader = IOFactory::createReaderForFile($filePath);
    $reader->setReadDataOnly(true);
    $reader->setReadFilter(new MyReadFilter());
    
    $spreadsheet = $reader->load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $row1 = $worksheet->toArray(null, true, true, true)[1];

    echo "ALL HEADERS:\n";
    foreach ($row1 as $col => $val) {
        echo "$col: $val\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
