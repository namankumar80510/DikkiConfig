<?php

declare(strict_types=1);

namespace Dikki\Config;

use Nette\Neon\Neon;

/**
 * Class NeonParser
 *
 * This class takes either the path to a neon file or a directory containing neon files.
 * If a directory is passed, it will parse all neon files in it and return an array.
 * Otherwise, it will parse the neon file and return an array.
 *
 * @package Dikki\Config
 */
class NeonParser implements ConfigInterface
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Parse neon file(s) and return an array.
     *
     * @return array
     */
    public function parse(): array
    {
        if (is_dir($this->path)) {
            $config = [];
            $files = scandir($this->path);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'neon') {
                    $config[pathinfo($file, PATHINFO_FILENAME)] = Neon::decode(file_get_contents($this->path . '/' . $file));
                }
            }
            return $config;
        } else {
            return Neon::decode(file_get_contents($this->path));
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
