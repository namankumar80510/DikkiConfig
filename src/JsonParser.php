<?php

namespace Dikki\Config;

/**
 * Class JsonParser
 *
 * This class takes either the path to a json file or a directory containing json files.
 * If a directory is passed, it will parse all json files in it and return an array.
 * Otherwise, it will parse the json file and return an array.
 *
 * @package Dikki\Config
 */
class JsonParser implements ConfigInterface
{

    private string|array $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * parse json file(s) and return an array
     *
     * @return false|array
     */
    public function parse(): false|array
    {
        // if a directory is passed, parse all json files in it and return an array
        if (is_dir($this->path)) {
            $config = [];
            $files = scandir($this->path);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                    $config[pathinfo($file, PATHINFO_FILENAME)] = json_decode(file_get_contents($this->path . '/' . $file), true);
                }
            }
            return $config;
        } else {
            // if a file is passed, parse it and return an array
            return json_decode(file_get_contents($this->path), true);
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