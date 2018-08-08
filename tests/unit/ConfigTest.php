<?php

namespace LeoProject\Tests;

use LeoProject\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testFileExists()
    {
        $this->assertFileExists(getcwd() . DIRECTORY_SEPARATOR . 'config.yml');
    }

    public function testLoad()
    {
        Config::load();
        $this->assertNotNull(Config::$config);
    }

    public function testGet()
    {
        Config::load();
        $config = Config::get();
        $this->assertNotNull($config);
        $this->assertObjectHasAttribute('fees', $config);
        $this->assertObjectHasAttribute('rates', $config);
    }
}
