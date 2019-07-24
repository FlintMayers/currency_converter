<?php

namespace Acme;

use DateTime;

class Commissioner
{
    const CASH_OUT_COMMISSIONS = 0.3;
    const CASH_IN_COMMISSIONS = 0.03;

    /**
     * @var array
     */
    private $customers;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @param Converter $converter
     */
    public function __construct(Converter $converter)
    {
        $this->customers = [];
        $this->converter = $converter;
    }

    /**
     * @param string $date
     * @param int $customerId
     * @param string $customerType
     * @param string $operationType
     * @param float $amount
     * @param string $currency
     * @return float
     * @throws
     */
    public function calculate(
        string $date,
        int $customerId,
        string $customerType,
        string $operationType,
        float $amount,
        string $currency
    ): float {
        $amount = $this->converter->toEUR($amount, $currency);

        if ($customerType === 'legal') {
            $commissions = self::CASH_OUT_COMMISSIONS / 100 * $amount;
            $convertedCommissions = $this->converter->toOriginalCurrency($commissions, $currency);
            $minimumCommissions = max($convertedCommissions, 0.50);

            return min($minimumCommissions, 5);
        }

        if ($operationType === 'cash_in') {
            $commissions = self::CASH_IN_COMMISSIONS / 100 * $amount;
            $convertedCommissions = $this->converter->toOriginalCurrency($commissions, $currency);

            return min($convertedCommissions, 5);
        }

        if (!array_key_exists($customerId, $this->customers)) {
            $this->customers[$customerId] = ['freeAmount' => 1000];
        }

        if (array_key_exists('date', $this->customers[$customerId])) {
            $current = new DateTime($date);
            $daysSinceLastTransfer = $current->diff($this->customers[$customerId]['date'])->format("%a");
            if ($daysSinceLastTransfer > 7) {
                $this->customers[$customerId]['freeAmount'] = 1000;
            }
        }

        $this->customers[$customerId]['date'] = new DateTime($date);
//        $this->customers[$customerId]['weekDay'] = date('w', strtotime($date));
        $freeAmount = $this->customers[$customerId]['freeAmount'];
        $commissionableAmount = max($amount - $freeAmount, 0);
        $this->customers[$customerId]['freeAmount'] = max($freeAmount - $amount, 0);
        $commissions = self::CASH_OUT_COMMISSIONS / 100 * $commissionableAmount;

        return $this->converter->toOriginalCurrency($commissions, $currency);
    }
}