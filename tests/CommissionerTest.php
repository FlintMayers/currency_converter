<?php

namespace Tests;

use Acme\Commissioner;
use PHPUnit\Framework\TestCase;

class CommissionerTest extends TestCase
{
    public function testConverterInstance(): void
    {
        $commissioner = new Commissioner();
        $this->assertInstanceOf(Commissioner::class, $commissioner);
    }

    public function testGivenOutputsForCustomerNo1(): void
    {
        $commissioner = new Commissioner();

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
    }

    public function testGivenOutputsForCustomerNo2(): void
    {
        $commissioner = new Commissioner();

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

    public function testGivenOutputsForCustomerNo3(): void
    {
        $commissioner = new Commissioner();

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
    }

    public function testGivenOutputsForCustomerNo4(): void
    {
        $commissioner = new Commissioner();

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
    }

    public function testMaximumCommissions(): void
    {
        $commissioner = new Commissioner();

        $this->assertEquals(
            5.00,
            $commissioner->calculate(
                '2016-02-19',
                5,
                'natural',
                'cash_in',
                18000,
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

    public function testFourthSmallTransactionWithinAWeekCommissionable(): void
    {
        $commissioner = new Commissioner();

        $commissioner->calculate(
            '2016-02-19', 1, 'natural', 'cash_out', 150, 'USD'
        );

        $commissioner->calculate(
            '2016-02-19', 1, 'natural', 'cash_out', 150, 'USD'
        );

        $commissioner->calculate(
            '2016-02-19', 1, 'natural', 'cash_out', 150, 'USD'
        );

        $this->assertEquals(
            0.60,
            $commissioner->calculate(
                '2016-02-19',
                1,
                'natural',
                'cash_out',
                200,
                'USD'
            )
        );
    }

    public function testFourthSmallTransactionNextWeekNonCommissionable(): void
    {
        $commissioner = new Commissioner();

        $commissioner->calculate(
            '2016-02-19', 1, 'natural', 'cash_out', 150, 'USD'
        );

        $commissioner->calculate(
            '2016-02-19', 1, 'natural', 'cash_out', 150, 'USD'
        );

        $commissioner->calculate(
            '2016-02-19', 1, 'natural', 'cash_out', 150, 'USD'
        );

        $this->assertEquals(
            0.0,
            $commissioner->calculate(
                '2016-02-22',
                1,
                'natural',
                'cash_out',
                200,
                'USD'
            )
        );
    }

    public function testInvalidInput(): void
    {
        $commissioner = new Commissioner();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount cannot be zero or less');

        $commissioner->calculate(
            '2016-02-19',
            1,
            'legal',
            'cash_in',
            -500,
            'EUR'
        );

        $this->expectExceptionMessage('Invalid operation type');

        $commissioner->calculate(
            '2016-02-19',
            1,
            'legal',
            'cash_flow',
            1,
            'EUR'
        );

        $this->expectExceptionMessage('Invalid customer type');

        $commissioner->calculate(
            '2016-02-19',
            1,
            'illegal',
            'cash_in',
            1,
            'EUR'
        );
    }
}
