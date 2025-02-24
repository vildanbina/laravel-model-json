<?php

namespace Vildanbina\ModelJson\Traits;

/**
 * Trait DataManipulator
 *
 * @package Vildanbina\ModelJson\Traits
 */
trait DataManipulator
{
    protected array $forgetKeys = [];

    /**
     * @param  string|array|null  $forgetKeys
     *
     * @return $this
     */
    public function setForgetData(null|string|array $forgetKeys): static
    {
        if (filled($forgetKeys)) {
            $this->forgetKeys = is_string($forgetKeys)
                ? explode(',', $forgetKeys)
                : $forgetKeys;
        }

        return $this;
    }
}
