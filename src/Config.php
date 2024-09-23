<?php

declare(strict_types=1);

namespace Dikki\Config;

/**
 * Class Config
 *
 * This class takes an instance of a class implementing ConfigInterface and delegates the get() method to it.
 *
 * @package Dikki\Config
 */
class Config implements ConfigInterface
{
    private ConfigInterface $parser;

    public function __construct(ConfigInterface $parser)
    {
        $this->parser = $parser;
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
        return $this->parser->get($key, $default);
    }

    public function getAll(): array
    {
        return $this->parse();
    }

    public function parse(): array
    {
        return $this->parser->parse();
    }
}
