<?php

require 'vendor/autoload.php';

use Acme\Commissioner;
use Acme\CSVParser;

$commissioner = new Commissioner();
$importer = new CSVParser($commissioner);
$allCommissions = $importer->parse('../' . $argv[1]);

foreach ($allCommissions as $commissions) {
    print $commissions . "\n";
}
