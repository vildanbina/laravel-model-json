<?php

namespace Vildanbina\ModelJson\Traits;

use BadMethodCallException;
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
     * @var class-string<Model>
     */
    protected string $model;

    /**
     * @var ?string
     */
    protected ?string $scope = null;

    /**
     * @var bool
     */
    protected bool $withoutGlobalScopes = false;

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
     * @param  ?string  $scope
     *
     * @return $this
     */
    public function setScope(?string $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function withoutGlobalScopes(bool $withoutGlobalScopes = true): static
    {
        $this->withoutGlobalScopes = $withoutGlobalScopes;

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
        return $this->model::query()
            ->when($this->withoutGlobalScopes, function (Builder $builder) {
                $builder->withoutGlobalScopes();
            })
            ->when(filled($relations = $this->getRelationships()), function (Builder $builder) use ($relations) {
                $builder->with($relations);
            })
            ->when(filled($scope = $this->scope), function (Builder $builder) use ($scope) {
                $args = explode(',', $scope);
                $scopeName = array_shift($args);
                empty($args) ? $builder->$scopeName() : $builder->$scopeName(...$args);
            });
    }
}
