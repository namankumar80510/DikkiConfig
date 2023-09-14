<?php

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

    public function __construct(private ConfigInterface $parser)
    {
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
        return $this->parser->get($key);
    }

}