<?php

namespace Vildanbina\ModelJson\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LogicException;

/**
 * Trait HasModel
 *
 * @package Vildanbina\ModelJson\Traits
 */
trait HasModel
{
    /**
     * @var string
     */
    protected string $model;

    /**
     * @param  string  $model
     *
     * @return $this
     */
    public function setModel(string $model): static
    {
        $this->model = $model;

        if (!class_exists($this->model)) {
            throw new ModelNotFoundException('Model ' . $this->model . ' does not exists.');
        }

        if (!new $this->model instanceof Model) {
            throw new LogicException('Class ' . $this->model . ' is not ' . Model::class . ' instance.');
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalModelsCount(): int
    {
        return $this->modelQuery()->count();
    }

    /**
     * @return Builder
     */
    protected function modelQuery(): Builder
    {
        return $this->model::query();
    }
}
