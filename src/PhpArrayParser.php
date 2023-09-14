<?php

namespace Dikki\Config;

/**
 * Class PhpArrayParser
 *
 * This class takes either the path to a php array file or a directory containing php array files.
 * If a directory is passed, it will parse all php array files in it and return an array.
 * Otherwise, it will parse the php array file and return an array.
 *
 * @package Dikki\Config
 */
class PhpArrayParser implements ConfigInterface
{

    private string|array $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * parse php array file(s) and return an array
     *
     * @return false|array
     */
    public function parse(): false|array
    {
        // if a directory is passed, parse all php array files in it and return an array
        if (is_dir($this->path)) {
            $config = [];
            $files = scandir($this->path);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $config[pathinfo($file, PATHINFO_FILENAME)] = require $this->path . '/' . $file;
                }
            }
            return $config;
        } else {
            // if a file is passed, parse it and return an array
            return require $this->path;
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