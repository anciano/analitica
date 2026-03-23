<?php

require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$pdfFile = 'storage/app/public/Clasificador SNSS - Año 2026_(14.01.26) (1).pdf';

if (!file_exists($pdfFile)) {
    die("Error: File $pdfFile not found.\n");
}

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFile);
    
    // For large PDFs, we might want to search specific strings rather than dumping everything
    $text = $pdf->getText();
    
    // Display sections related to 22.04 and 22.12
    echo "--- SNSS CLASSIFIER 2026 SECTIONS 22.04 & 22.12 ---\n";
    
    $lines = explode("\n", $text);
    foreach ($lines as $line) {
        if (strpos($line, '22 04 004') !== false || strpos($line, '22 04 005') !== false || strpos($line, '22 12 999') !== false) {
            echo $line . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
