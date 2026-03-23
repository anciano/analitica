<?php

require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$pdfFile = 'storage/app/public/Plan de Cuentas_año_ 2026_(14.01.26).pdf';

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFile);
    $text = $pdf->getText();
    
    $lines = explode("\n", $text);
    echo "--- SEARCHING FOR PRONEIS / AYUDAS TECNICAS ---\n";
    
    foreach ($lines as $i => $line) {
        if (stripos($line, 'Proneis') !== false || stripos($line, 'Ayudas Tecnicas') !== false || stripos($line, 'Ortesis') !== false) {
             echo "LINE " . ($i+1) . ": " . trim($line) . "\n";
             if (isset($lines[$i+1])) echo "  [+1] " . trim($lines[$i+1]) . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
