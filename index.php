<?php

/**
 * Leo's test task
 */

use LeoProject\Config;
use LeoProject\Helpers;
use LeoProject\Fees;

require __DIR__ . '/vendor/autoload.php';

try {
    // check PHP version
    Helpers::checkPhpVersion();

    // get filename from the first command line parameter
    $filename = Helpers::getFilename();

    // load configuration data
    Config::load();

    // prepare fees obj instance using configuration and data from the file passed
    $fees = new Fees($filename, Config::get());

    // calculate fees and get array of fees
    $result = $fees->calculate();

    // format and show the result
    Helpers::out(
        implode(
            "\n",
            array_map(function ($x) {
                return number_format($x, 2);
            }, $fees->getResult())
        )
    );
} catch (\Exception $e) {
    Helpers::out($e->getMessage(), 'ERROR');
}
