<?php

namespace Tests;

use Acme\Commissioner;
use Acme\Converter;
use Acme\CSVParser;
use PHPUnit\Framework\TestCase;

class CSVParserTest extends TestCase
{
    public function testParseOfGivenDate(): void
    {
        $converter = new Converter();
        $commissioner = new Commissioner($converter);
        $importer = new CSVParser($commissioner);
        $allCommissions = $importer->parse('../transactions.csv');
        $this->assertIsArray($allCommissions);
        $expectedResult = [
            0 => 0.6,
            1 => 3,
            2 => 0,
            3 => 0.06,
            4 => 0.9,
            5 => 0,
            6 => 0.7,
            7 => 0.3,
            8 => 0.3,
            9 => 5,
            10 => 0,
            11 => 0,
            12 => 8612,
        ];
        $this->assertEquals($expectedResult, $allCommissions);
    }
}