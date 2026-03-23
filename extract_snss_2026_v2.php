<?php

require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$pdfFile = 'storage/app/public/Clasificador SNSS - Año 2026_(14.01.26) (1).pdf';

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFile);
    $text = $pdf->getText();
    
    $lines = explode("\n", $text);
    echo "--- BROAD SEARCH SNSS 2026 ---\n";
    
    $keywords = ['22 04 004', '22 04 005', '22 12 999', 'Productos Farmac', 'Insumos', 'Farmacia'];
    
    foreach ($lines as $i => $line) {
        $found = false;
        foreach ($keywords as $kw) {
            if (stripos($line, $kw) !== false) {
                $found = true;
                break;
            }
        }
        
        if ($found) {
            // Print context (previous line, current, next)
            // if (isset($lines[$i-1])) echo "  [context] " . trim($lines[$i-1]) . "\n";
            echo "> " . trim($line) . "\n";
            // if (isset($lines[$i+1])) echo "  [context] " . trim($lines[$i+1]) . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
