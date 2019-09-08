<?php

namespace Acme;

use League\Csv\Reader;

/**
 * Class CSVParser
 */
class CSVParser implements ParserInterface
{
    /**
     * @var Commissioner
     */
    private $commissioner;

    /**
     * CSVParser constructor.
     *
     * @param Commissioner $commissioner
     */
    public function __construct(Commissioner $commissioner)
    {
        $this->commissioner = $commissioner;
    }

    /**
     * @param string $path
     * @return array
     */
    public function parse(string $path): array
    {
        $reader = Reader::createFromPath(__DIR__ . '/' . $path, 'r');
        $transactions = $reader->getRecords();
        $allCommissions = [];
        foreach ($transactions as $transactionData) {
            $allCommissions[] = $this->commissioner->calculate(...$transactionData);
        }

        return $allCommissions;
    }
}