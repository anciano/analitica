<?php

require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$pdfFile = 'storage/app/public/Plan de Cuentas_año_ 2026_(14.01.26).pdf';

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFile);
    $text = $pdf->getText();
    
    // Remove spaces and newlines for a cleaner search
    $cleanText = str_replace([' ', "\n", "\r", "\t"], '', $text);
    
    $searchCodes = ['2204004002', '2204004003', '2204004004', '2204004005', '2204004006'];
    
    echo "--- EXACT SEARCH PLAN DE CUENTAS 2026 ---\n";
    foreach ($searchCodes as $code) {
        if (strpos($cleanText, $code) !== false) {
            echo "MATCH FOUND: $code\n";
            // Find context in original text
            if (preg_match("/.{0,50}$code.{0,100}/s", $text, $matches)) {
                 echo "  Context: " . trim($matches[0]) . "\n";
            }
        } else {
            echo "NOT FOUND: $code\n";
        }
    }
    
    // Also look for descriptions
    $keywords = ['Proneis', 'Ayudas Técnicas', 'Otros Productos Farmac'];
    foreach ($keywords as $kw) {
        if (stripos($text, $kw) !== false) {
             echo "KEYWORD FOUND: $kw\n";
             if (preg_match("/.{0,50}$kw.{0,100}/si", $text, $matches)) {
                 echo "  Context: " . trim($matches[0]) . "\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
