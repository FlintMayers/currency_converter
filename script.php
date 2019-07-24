<?php

require 'vendor/autoload.php';

use Acme\Commissioner;
use Acme\Converter;
use Acme\CSVParser;

$converter = new Converter();
$commissioner = new Commissioner($converter);
$importer = new CSVParser($commissioner);
$allCommissions = $importer->parse('../' . $argv[1]);

foreach ($allCommissions as $commissions) {
    print $commissions . "\n";
}
