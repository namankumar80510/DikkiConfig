<?php

namespace Dikki\Config;

interface ConfigInterface
{
    public function get(string $key);
}