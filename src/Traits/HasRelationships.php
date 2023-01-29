<?php

namespace Vildanbina\ModelJson\Traits;

/**
 * Trait HasRelationships
 *
 * @package Vildanbina\ModelJson\Traits
 */
trait HasRelationships
{
    /**
     * @var string|array|null
     */
    protected null|string|array $relationships = [];

    /**
     * @return array|string|null
     */
    public function getRelationships(): null|array|string
    {
        return $this->relationships;
    }

    /**
     * @param  array|string|null  $relationships
     *
     * @return $this
     */
    public function setRelationships(null|array|string $relationships): static
    {
        $this->relationships = filled($relationships) ? explode('+', $relationships) : [];

        return $this;
    }
}
