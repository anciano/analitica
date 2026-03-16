<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class HeaderFilter implements IReadFilter {
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool {
        return $row == 1;
    }
}

$inputFileName = 'EGRESOS HOSPITALIZARIOS 2018 - 2025 HRC (1).xlsx';
$reader = IOFactory::createReaderForFile($inputFileName);
$reader->setReadDataOnly(true);
$reader->setReadFilter(new HeaderFilter());

$spreadsheet = $reader->load($inputFileName);
$worksheet = $spreadsheet->getActiveSheet();
$headers = $worksheet->toArray()[0];

foreach ($headers as $index => $header) {
    echo "[$index] => " . $header . "\n";
}
