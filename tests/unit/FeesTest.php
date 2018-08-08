<?php

namespace LeoProject\Tests;

use LeoProject\Config;
use LeoProject\Fees;

class FeesTest extends \PHPUnit_Framework_TestCase
{
    public function testDataFileExists()
    {
        $this->assertFileExists(getcwd() . DIRECTORY_SEPARATOR . 'test.csv');
    }

    public function testConvertCurrency()
    {
        Config::load();
        $config = Config::get();
        // eur:zzz = 2
        $config->rates->zzz = 2;
        $fees = new Fees('test.csv', $config);
        // zzz to eur
        $this->assertEquals(5, $fees->convertCurrency('zzz', 10));
        // eur to zzz
        $this->assertEquals(10, $fees->convertCurrency('zzz', 5, true));
    }

    public function testCalculateRow()
    {
        Config::load();
        $config = Config::get();
        // eur:zzz = 2
        $config->rates->zzz = 2;
        $fees = new Fees('test.csv', $config);
        $a = ['2016-01-05', 1, 'natural', 'cash_in', 1000, 'ZZZ'];
        $this->assertEquals(0.3, $fees->calculateRow($a));
        //
        $b = ['2016-01-05', 1, 'natural', 'cash_in', 100000, 'EUR'];
        $this->assertEquals(5, $fees->calculateRow($b));
        //
        $c = ['2016-01-05', 1, 'natural', 'cash_in', 100, 'EUR'];
        $this->assertEquals(0.03, $fees->calculateRow($c));
    }
}
