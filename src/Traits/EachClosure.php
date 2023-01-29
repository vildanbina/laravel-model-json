<?php

namespace Vildanbina\ModelJson\Traits;

use Closure;

/**
 * Trait EachClosure
 *
 * @package Vildanbina\ModelJson\Traits
 */
trait EachClosure
{
    /**
     * @var Closure|null
     */
    protected null|Closure $onEach = null;

    /**
     * @param  Closure  $onEach
     *
     * @return $this
     */
    public function onEach(Closure $onEach): static
    {
        $this->onEach = $onEach;
        return $this;
    }
}
