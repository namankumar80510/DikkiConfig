<?php

declare(strict_types=1);

namespace Dikki\Config;

/**
 * Class ConfigFetcher.
 *
 * Pass a path to the constructor and it will recursively search for any and every supported file format:
 * - ini, php (array), .env, json, yaml, ini
 *
 * Parses each of them and returns the array.
 */
class ConfigFetcher
{
    private string $path;
    private array $parsers;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->parsers = [
            'php' => PhpArrayParser::class,
            'ini' => IniParser::class,
            'json' => JsonParser::class,
            'yaml' => YamlParser::class,
            'neon' => NeonParser::class,
            'env' => DotEnvParser::class
        ];
    }

    /**
     * Fetch and parse all supported config files.
     *
     * @return array
     */
    public function fetch(): array
    {
        $config = [];
        $files = $this->getFiles($this->path);

        foreach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if (isset($this->parsers[$extension])) {
                $parserClass = $this->parsers[$extension];
                $parser = new $parserClass($file);
                $parsedConfig = $parser->parse();
                $config = $this->mergeConfigs($config, $parsedConfig);
            }
        }

        return $config;
    }

    /**
     * Recursively get all files from the given directory.
     *
     * @param string $dir
     * @return array
     */
    private function getFiles(string $dir): array
    {
        $files = [];
        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $files = array_merge($files, $this->getFiles($path));
            } else {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * Merge two configuration arrays, preserving nested keys.
     *
     * @param array $baseConfig
     * @param array $newConfig
     * @return array
     */
    private function mergeConfigs(array $baseConfig, array $newConfig): array
    {
        foreach ($newConfig as $key => $value) {
            if (is_array($value) && isset($baseConfig[$key]) && is_array($baseConfig[$key])) {
                $baseConfig[$key] = $this->mergeConfigs($baseConfig[$key], $value);
            } else {
                $baseConfig[$key] = $value;
            }
        }

        return $baseConfig;
    }

    /**
     * Get a specific configuration value by key using dot notation.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $config = $this->fetch();
        $keys = explode('.', $key);

        foreach ($keys as $keyPart) {
            if (is_array($config) && array_key_exists($keyPart, $config)) {
                $config = $config[$keyPart];
            } else {
                return $default;
            }
        }

        return $config;
    }
}
