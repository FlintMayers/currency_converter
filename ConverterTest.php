<?php

use Acme\Converter;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    public function testTruth(): void
    {
        $converter = new Converter();
        $this->assertInstanceOf(Converter::class, $converter);
    }

}