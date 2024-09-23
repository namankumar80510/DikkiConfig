<?php

declare(strict_types=1);

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
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Parse json file(s) and return an array.
     *
     * @return array
     */
    public function parse(): array
    {
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
            return json_decode(file_get_contents($this->path), true);
        }
    }

    /**
     * Get a value from the config array.
     *
     * Dot notation is supported, e.g. 'database.host' will return the value of $config['database']['host']
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $config = $this->parse();
        $keys = explode('.', $key);
        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                return $default;
            }
            $config = $config[$key];
        }
        return $config;
    }
}
