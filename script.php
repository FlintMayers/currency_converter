<?php

require 'vendor/autoload.php';

use Acme\Commissioner;
use Acme\CSVParser;

$fileName = $argv[1];
$filePath = __DIR__ . '/' . $fileName;

validateInput($fileName, $filePath);

$commissioner = new Commissioner();
$importer = new CSVParser($commissioner);
$allCommissions = $importer->parse($filePath);

foreach ($allCommissions as $commissions) {
    print $commissions . PHP_EOL;
}

/**
 * @param string $fileName
 * @param string $filePath
 */
function validateInput($fileName, string $filePath): void
{
    if (!isset($fileName)) {
        exit('No csv file provided' . PHP_EOL);
    }
    if (!file_exists($filePath)) {
        exit('Provided file doesn\'t exist' . PHP_EOL);
    }
    if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'csv') {
        exit('File extension must be csv' . PHP_EOL);
    }
}
