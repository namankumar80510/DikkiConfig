<?php

declare(strict_types=1);

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
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Parse ini file(s) and return an array.
     *
     * @return array
     */
    public function parse(): array
    {
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
            return parse_ini_file($this->path, true);
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
