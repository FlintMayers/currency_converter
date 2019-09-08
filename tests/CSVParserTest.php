<?php

namespace Tests;

use Acme\Commissioner;
use Acme\CSVParser;
use PHPUnit\Framework\TestCase;

class CSVParserTest extends TestCase
{
    public function testParseGivenData(): void
    {
        $parser = new CSVParser((new Commissioner()));
        $commissions = $parser->parse('../transactions.csv');
        $this->assertIsArray($commissions);
        $expectedResult = [
            0.6,
            3,
            0,
            0.06,
            0.9,
            0,
            0.7,
            0.3,
            0.3,
            5,
            0,
            0,
            8612,
        ];
        $this->assertEquals($expectedResult, $commissions);
    }
}