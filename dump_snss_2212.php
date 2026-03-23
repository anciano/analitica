<?php

require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$pdfFile = 'storage/app/public/Clasificador SNSS - Año 2026_(14.01.26) (1).pdf';

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFile);
    $text = $pdf->getText();
    
    $lines = explode("\n", $text);
    echo "--- DUMP AROUND 22 12 999 ---\n";
    
    $start = false;
    foreach ($lines as $i => $line) {
        if (strpos($line, '22 12 999') !== false) {
            $start = true;
        }
        
        if ($start) {
            echo "> " . trim($line) . "\n";
            // Stop after some lines or when moving to 23
            if (strpos($line, '23 ') !== false) break;
            if ($i > 10000) break; // Safety
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
