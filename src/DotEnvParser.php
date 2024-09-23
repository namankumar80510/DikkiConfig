<?php

declare(strict_types=1);

namespace Dikki\Config;

/**
 * Class DotEnvParser
 *
 * This class takes either the path to a dotenv file or a directory containing dotenv files.
 * If a directory is passed, it will parse all dotenv files in it and return an array.
 * Otherwise, it will parse the dotenv file and return an array.
 *
 * @package Dikki\Config
 */
class DotEnvParser implements ConfigInterface
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Parse dotenv file(s) and return an array.
     *
     * @return array
     */
    public function parse(): array
    {
        if (is_dir($this->path)) {
            $config = [];
            $files = scandir($this->path);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'env') {
                    $config[pathinfo($file, PATHINFO_FILENAME)] = $this->parseDotEnvFile($this->path . '/' . $file);
                }
            }
            return $config;
        } else {
            return $this->parseDotEnvFile($this->path);
        }
    }

    /**
     * Parse a single dotenv file and return an array.
     *
     * @param string $filePath
     * @return array
     */
    private function parseDotEnvFile(string $filePath): array
    {
        $config = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value);
        }
        return $config;
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
