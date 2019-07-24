<?php

namespace Tests;

use Acme\Commissioner;
use Acme\Converter;
use PHPUnit\Framework\TestCase;

class CommissionerTest extends TestCase
{
    public function testConverterInstance(): void
    {
        $converter = new Converter();
        $commissioner = new Commissioner($converter);
        $this->assertInstanceOf(Commissioner::class, $commissioner);
    }

    public function testCalculateCommissions(): void
    {
        $converter = new Converter();
        $commissioner = new Commissioner($converter);

        $this->assertEquals(
            0.60,
            $commissioner->calculate(
                '2014-12-31',
                4,
                'natural',
                'cash_out',
                1200,
                'EUR'
            )
        );
        $this->assertEquals(
            3,
            $commissioner->calculate(
                '2015-01-01',
                4,
                'natural',
                'cash_out',
                1000,
                'EUR'
            )
        );

        $this->assertEquals(
            0,
            $commissioner->calculate(
                '2016-01-05',
                4,
                'natural',
                'cash_out',
                1000,
                'EUR'
            )
        );

        $this->assertEquals(
            0.06,
            $commissioner->calculate(
                '2016-01-05',
                1,
                'natural',
                'cash_in',
                200,
                'EUR'
            )
        );

        $this->assertEquals(
            0.90,
            $commissioner->calculate(
                '2016-01-06',
                2,
                'legal',
                'cash_out',
                300,
                'EUR'
            )
        );

        $this->assertEquals(
            0,
            $commissioner->calculate(
                '2016-01-06',
                1,
                'natural',
                'cash_out',
                30000,
                'JPY'
            )
        );

        $this->assertEquals(
            0.70,
            $commissioner->calculate(
                '2016-01-07',
                1,
                'natural',
                'cash_out',
                1000,
                'EUR'
            )
        );

        $this->assertEquals(
            0.30,
            $commissioner->calculate(
                '2016-01-07',
                1,
                'natural',
                'cash_out',
                100,
                'USD'
            )
        );
        $this->assertEquals(
            0.30,
            $commissioner->calculate(
                '2016-01-10',
                1,
                'natural',
                'cash_out',
                100,
                'EUR'
            )
        );

        $this->assertEquals(
            5.00,
            $commissioner->calculate(
                '2016-01-10',
                2,
                'legal',
                'cash_in',
                1000000.00,
                'EUR'
            )
        );

        $this->assertEquals(
            0.00,
            $commissioner->calculate(
                '2016-01-10',
                3,
                'natural',
                'cash_out',
                1000.00,
                'EUR'
            )
        );

        $this->assertEquals(
            0.00,
            $commissioner->calculate(
                '2016-02-15',
                1,
                'natural',
                'cash_out',
                300.00,
                'EUR'
            )
        );

        $this->assertEquals(
            8612,
            $commissioner->calculate(
                '2016-02-19',
                2,
                'natural',
                'cash_out',
                3000000,
                'JPY'
            )
        );
    }

    public function testMaximumCommissions()
    {
        $converter = new Converter();
        $commissioner = new Commissioner($converter);

        $this->assertEquals(
            5.00,
            $commissioner->calculate(
                '2016-02-19',
                5,
                'natural',
                'cash_in',
                17000,
                'USD'
            )
        );

        $this->assertEquals(
            4.95,
            $commissioner->calculate(
                '2016-02-19',
                5,
                'natural',
                'cash_in',
                16500,
                'USD'
            )
        );

        $this->assertEquals(
            0.50,
            $commissioner->calculate(
                '2016-02-19',
                5,
                'legal',
                'cash_in',
                5,
                'EUR'
            )
        );
    }
}