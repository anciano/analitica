<?php

use Smalot\PdfParser\Parser;

require_once 'vendor/autoload.php';

echo "Attempting to initialize Parser...\n";

try {
    $parser = new Parser();
    $pdf = $parser->parseFile('storage/app/public/Organigrama HRC 2025 (4 dic).pdf');
    echo $pdf->getText();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
