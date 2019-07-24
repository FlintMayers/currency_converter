<?php

namespace Acme;

/**
 * Class Converter
 */
class Converter
{
    /**
     * @var array
     */
    private $exchangeRates = [
        'USD' => 1.1497,
        'JPY' => 129.53,
        'EUR' => 1,
    ];

    /**
     * @param float $amount
     * @param string $currency
     * @return float
     */
    public function toEUR(float $amount, string $currency): float
    {
        return $amount / $this->exchangeRates[$currency];
    }

    /**
     * @param float $commissions
     * @param string $currency
     * @return float
     */
    public function toOriginalCurrency(float $commissions, string $currency): float
    {
        $commissionInEUR = $commissions * $this->exchangeRates[$currency];
        if ($currency === 'JPY') {
            return ceil($commissionInEUR);
        }

        $decimalCount = strlen($commissionInEUR) - strrpos($commissionInEUR, '.') - 1;
        if ($decimalCount >= 2) {
            return $this->ceilToTwoDecimalPoints($commissionInEUR);
        }

        return $commissionInEUR;
    }

    /**
     * @param float $commissionInEUR
     * @return float
     */
    private function ceilToTwoDecimalPoints(float $commissionInEUR): float
    {
        $pow = pow(10, 2);

        return (ceil($pow * $commissionInEUR) + ceil($pow * $commissionInEUR - ceil($pow * $commissionInEUR))) / $pow;
    }
}