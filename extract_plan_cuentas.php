<?php

require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$pdfFile = 'storage/app/public/Plan de Cuentas_año_ 2026_(14.01.26).pdf';

if (!file_exists($pdfFile)) {
    die("Error: File $pdfFile not found.\n");
}

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFile);
    $text = $pdf->getText();
    
    echo "--- PLAN DE CUENTAS 2026 SECTIONS 22.04 ---\n";
    
    $lines = explode("\n", $text);
    foreach ($lines as $i => $line) {
        if (strpos($line, '2204004') !== false || strpos($line, '2204 004') !== false) {
            echo "> " . trim($line) . "\n";
            // Print context
            if (isset($lines[$i+1])) echo "  [next] " . trim($lines[$i+1]) . "\n";
            if (isset($lines[$i+2])) echo "  [next] " . trim($lines[$i+2]) . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
