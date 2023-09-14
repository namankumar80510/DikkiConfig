<?php

namespace Dikki\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlParser
 *
 * This class takes either the path to an yaml file or a directory containing yaml files.
 * If a directory is passed, it will parse all yaml files in it and return an array.
 * Otherwise, it will parse the yaml file and return an array.
 *
 * @package Dikki\Config
 */
class YamlParser implements \Dikki\Config\ConfigInterface
{

    private string|array $path;
    private Yaml $yamlParser;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->yamlParser = new Yaml();
    }

    /**
     * parse yaml file(s) and return an array
     *
     * @return false|array
     */
    public function parse(): false|array
    {
        // if a directory is passed, parse all yaml files in it and return an array
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
            // if a file is passed, parse it and return an array
            return $this->yamlParser->parseFile($this->path);
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
            if (!isset($config[$key])) {
                return null;
            }
            $config = $config[$key];
        }
        return $config;
    }

}