<?php

declare(strict_types=1);

namespace Dikki\Config;

interface ConfigInterface
{
    public function parse(): array;

    public function get(string $key, mixed $default = null): mixed;
}
