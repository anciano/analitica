<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$inputFileName = 'storage/app/public/clasificador.xlsx';
$spreadsheet = IOFactory::load($inputFileName);

foreach ($spreadsheet->getSheetNames() as $sheetName) {
    echo "Sheet: " . $sheetName . "\n";
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    $rows = $worksheet->toArray();
    echo json_encode(array_slice($rows, 0, 5)) . "\n";
}
