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
    private array $configCache = [];

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
    public function fetchAllConfigs(): array
    {
        if (!empty($this->configCache)) {
            return $this->configCache;
        }

        $config = [];
        $files = $this->getFiles($this->path);

        foreach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $filename = pathinfo($file, PATHINFO_FILENAME);
            if (isset($this->parsers[$extension])) {
                $parserClass = $this->parsers[$extension];
                $parser = new $parserClass($file);
                $parsedConfig = $parser->parse();
                if ($extension === 'env') {
                    $config = array_merge($config, $parsedConfig);
                } else {
                    $config[$filename] = $parsedConfig;
                }
            }
        }

        $this->configCache = $config;
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
     * Get a specific configuration value by key using dot notation.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $config = $this->fetchAllConfigs();
        $keys = explode('.', $key);
        
        if (count($keys) === 1) {
            return $config[$key] ?? $default;
        }
        
        $filename = array_shift($keys);
        
        if (!isset($config[$filename])) {
            return $default;
        }
        
        $value = $config[$filename];
        
        foreach ($keys as $keyPart) {
            if (is_array($value) && array_key_exists($keyPart, $value)) {
                $value = $value[$keyPart];
            } else {
                return $default;
            }
        }

        return $value;
    }
}
