<?php

declare(strict_types=1);

namespace Dikki\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlParser
 *
 * This class takes either the path to a yaml file or a directory containing yaml files.
 * If a directory is passed, it will parse all yaml files in it and return an array.
 * Otherwise, it will parse the yaml file and return an array.
 *
 * @package Dikki\Config
 */
class YamlParser implements ConfigInterface
{
    private string $path;
    private Yaml $yamlParser;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->yamlParser = new Yaml();
    }

    /**
     * Parse yaml file(s) and return an array.
     *
     * @return array
     */
    public function parse(): array
    {
        if (is_dir($this->path)) {
            $config = [];
            $files = scandir($this->path);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'yaml') {
                    $config[pathinfo($file, PATHINFO_FILENAME)] = $this->yamlParser->parseFile($this->path . '/' . $file);
                }
            }
            return $config;
        } else {
            return $this->yamlParser->parseFile($this->path);
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
