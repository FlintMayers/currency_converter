<?php

namespace Acme;

use Acme\Utils\CurrencyConverter;
use DateTime;
use Exception;

/**
 * Class Commissioner
 */
class Commissioner
{
    const CASH_OUT_COMMISSIONS = 0.3;
    const CASH_IN_COMMISSIONS = 0.03;
    const CUSTOMER_TYPE_LEGAL = 'legal';
    const CUSTOMER_TYPE_NATURAL = 'natural';
    const OPERATION_TYPE_CASH_IN = 'cash_in';
    const OPERATION_TYPE_CASH_OUT = 'cash_out';

    /**
     * @var array
     */
    private $customers = [];

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
        $this->validateInput($customerType, $operationType, $amount);

        $amount = CurrencyConverter::toEUR($amount, $currency);

        if ($customerType === self::CUSTOMER_TYPE_LEGAL) {
            $commissions = self::CASH_OUT_COMMISSIONS / 100 * $amount;
            $convertedCommissions = CurrencyConverter::toOriginalCurrency($commissions, $currency);
            $minimumCommissions = max($convertedCommissions, 0.50);

            return min($minimumCommissions, 5);
        }

        if ($operationType === self::OPERATION_TYPE_CASH_IN) {
            return $this->cashInCommissions($amount, $currency);
        }

        $commissionableAmount = $this->getCommissionableAmount($date, $customerId, $amount);
        $commissions = self::CASH_OUT_COMMISSIONS / 100 * $commissionableAmount;

        return CurrencyConverter::toOriginalCurrency($commissions, $currency);
    }

    /**
     * @param float $amount
     * @param string $currency
     * @return float
     */
    private function cashInCommissions(float $amount, string $currency): float
    {
        $commissions = self::CASH_IN_COMMISSIONS / 100 * $amount;
        $convertedCommissions = CurrencyConverter::toOriginalCurrency($commissions, $currency);

        return min($convertedCommissions, 5);
    }

    /**
     * @param string $date
     * @param int $customerId
     * @param float $amount
     * @return float
     * @throws Exception
     */
    private function getCommissionableAmount(string $date, int $customerId, float $amount): float
    {
        if (!array_key_exists($customerId, $this->customers)) {
            $this->customers[$customerId] = ['freeAmount' => 1000, 'withdrawals' => 0];
        }

        $this->customers[$customerId]['withdrawals'] += 1;

        if ($this->customers[$customerId]['withdrawals'] > 3) {
            $this->customers[$customerId]['freeAmount'] = 0;
        }

        if ($this->transferHappensNextWeek($date, $customerId)) {
            $this->customers[$customerId]['freeAmount'] = 1000;
        }

        $this->customers[$customerId]['date'] = new DateTime($date);
        $this->customers[$customerId]['weekDay'] = date('N', strtotime($date));
        $freeAmount = $this->customers[$customerId]['freeAmount'];
        $commissionableAmount = max($amount - $freeAmount, 0);
        $this->customers[$customerId]['freeAmount'] = max($freeAmount - $amount, 0);

        return $commissionableAmount;
    }

    /**
     * @param string $date
     * @param int $customerId
     * @return bool
     * @throws Exception
     */
    private function transferHappensNextWeek(string $date, int $customerId): bool
    {
        if (!array_key_exists('date', $this->customers[$customerId])) {
            return false;
        }

        $currentDate = new DateTime($date);
        $daysSinceLastTransfer = $currentDate->diff($this->customers[$customerId]['date'])->format("%a");
        $currentWeekday = date('N', strtotime($date));
        $previousWeekday = $this->customers[$customerId]['weekDay'];

        return $daysSinceLastTransfer > 7 || $previousWeekday > $currentWeekday;
    }

    /**
     * @return array
     */
    private function getOperationTypes(): array
    {
        return [
            self::OPERATION_TYPE_CASH_IN,
            self::OPERATION_TYPE_CASH_OUT,
        ];
    }

    /**
     * @return array
     */
    private function getCustomerTypes(): array
    {
        return [
            self::CUSTOMER_TYPE_LEGAL,
            self::CUSTOMER_TYPE_NATURAL
        ];
    }

    /**
     * @param string $customerType
     * @param string $operationType
     * @param float $amount
     */
    private function validateInput(string $customerType, string $operationType, float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount cannot be zero or less');
        }

        if (!in_array($operationType, $this->getOperationTypes())) {
            throw new \InvalidArgumentException('Invalid operation type');
        }

        if (!in_array($customerType, $this->getCustomerTypes())) {
            throw new \InvalidArgumentException('Invalid customer type');
        }
    }
}