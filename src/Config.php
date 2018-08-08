<?php
/**
 * Loads YML configuration
 */

namespace LeoProject;

use Helpers;
use Symfony\Component\Yaml\Yaml;

class Config
{
    private const FILENAME = 'config.yml';

    public static $config = null;

    public static function load()
    {
        $f = getcwd() . DIRECTORY_SEPARATOR . self::FILENAME;
        try {
            if (!file_exists($f)) {
                throw new \Exception('Unable to load config file: ' . $f);
            }
            self::$config = Yaml::parse(file_get_contents($f), Yaml::PARSE_OBJECT_FOR_MAP);
        } catch (ParseException $e) {
            Helpers::out('Unable to parse the YAML string: ' . $e->getMessage(), 'ERROR');
        }
    }

    public static function get()
    {
        return self::$config;
    }
}
