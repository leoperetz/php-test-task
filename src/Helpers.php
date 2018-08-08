<?php
/**
 * Helper functions
 */

namespace LeoProject;

class Helpers
{
    /**
     * write string to std out
     */
    public static function out($str = false, $type = false)
    {
        fwrite(STDOUT, ($type ? $type . ': ' : '') . ($str ? $str : '') . "\n");
    }

    /**
     * check PHP version
     * and show the warning message if not match
     */
    public static function checkPhpVersion()
    {
        if (version_compare(PHP_VERSION, '7.1', '<')) {
            Helpers::out('PHP version 7.1 or greater is expected', 'WARNING');
        }
    }

    /**
     * get file name from from the 1st parameter of command line
     */
    public static function getFilename()
    {
        global $argc, $argv;
        if ($argc <= 0) {
            throw new \Exception('No arguments');
        }
        $filename = $argv[1] ?? false;
        if (!$filename) {
            throw new \Exception('No filename');
        }
        if (!file_exists($filename)) {
            throw new \Exception('The file does not exist: ' . $filename);
        }
        return $filename;
    }
}
