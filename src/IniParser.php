<?php

namespace Dikki\Config;

/**
 * Class IniParser
 *
 * This class takes either the path to an ini file or a directory containing ini files.
 * If a directory is passed, it will parse all ini files in it and return an array.
 * Otherwise, it will parse the ini file and return an array.
 *
 * @package Dikki\Config
 */
class IniParser implements ConfigInterface
{

    private string|array $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * parse ini file(s) and return an array
     *
     * @return false|array
     */
    public function parse(): false|array
    {
        // if a directory is passed, parse all ini files in it and return an array
        if (is_dir($this->path)) {
            $config = [];
            $files = scandir($this->path);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'ini') {
                    $config[pathinfo($file, PATHINFO_FILENAME)] = parse_ini_file($this->path . '/' . $file, true);
                }
            }
            return $config;
        } else {
            // if a file is passed, parse it and return an array
            return parse_ini_file($this->path, true);
        }
    }

    /**
     * get a value from the config array
     *
     * Dot notation is supported, e.g. 'database.host' will return the value of $config['database']['host']
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        $config = $this->parse();
        $keys = explode('.', $key);
        foreach ($keys as $key) {
            $config = $config[$key];
        }
        return $config;
    }

}