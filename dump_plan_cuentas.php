<?php

require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$pdfFile = 'storage/app/public/Plan de Cuentas_año_ 2026_(14.01.26).pdf';

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFile);
    $text = $pdf->getText();
    
    $lines = explode("\n", $text);
    echo "--- FULL 22 04 SECTION DUMP ---\n";
    
    $start = false;
    foreach ($lines as $i => $line) {
        if (strpos($line, '2204') !== false || strpos($line, '22 04') !== false) {
            $start = true;
        }
        
        if ($start) {
            echo "> " . trim($line) . "\n";
            // Stop after some lines or when moving to 22 05
            if (strpos($line, '2205') !== false || strpos($line, '22 05') !== false) break;
            if ($i > 6000) break; // Safety
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
