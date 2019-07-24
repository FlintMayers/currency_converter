<?php

namespace Acme;

use League\Csv\Reader;

/**
 * Class CSVParser
 */
class CSVParser
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
     * @param  string $path
     * @return array
     */
    public function parse(string $path): array
    {
        $reader = Reader::createFromPath(__DIR__ . '/' . $path, 'r');
        $transactions = $reader->getRecords();
        $allCommissions = [];
        foreach ($transactions as $t) {
            $allCommissions[] = $this->commissioner->calculate($t[0], $t[1], $t[2], $t[3], $t[4], $t[5]);
        }

        return $allCommissions;
    }
}