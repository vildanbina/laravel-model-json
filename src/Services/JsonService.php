<?php

namespace Vildanbina\ModelJson\Services;

/**
 * Class JsonService
 *
 * @package Vildanbina\ModelJson\Services
 */
abstract class JsonService
{
    /**
     * @return static
     */
    public static function make(): static
    {
        return new static;
    }

    /**
     * @return bool|string
     */
    abstract public function run(): bool|string;
}
