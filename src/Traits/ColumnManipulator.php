<?php

namespace Vildanbina\ModelJson\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Trait ColumnManipulator
 *
 * @package Vildanbina\ModelJson\Traits
 */
trait ColumnManipulator
{
    /**
     * @var array|string[]
     */
    static array $timestampsField = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var bool
     */
    protected bool $withoutTimestamps = false;

    /**
     * @var bool
     */
    protected bool $withHidden = false;

    /**
     * @var array
     */
    protected array $exceptColumns = [];

    /**
     * @var array
     */
    protected array $onlyColumns = [];

    /**
     * @param  bool  $withoutTimestamps
     *
     * @return $this
     */
    public function withoutTimestamps(bool $withoutTimestamps = true): static
    {
        $this->withoutTimestamps = $withoutTimestamps;

        return $this;
    }

    /**
     * @param  bool  $withHidden
     *
     * @return $this
     */
    public function withHidden(bool $withHidden = true): static
    {
        $this->withHidden = $withHidden;

        return $this;
    }

    /**
     * @param  string|array|null  $exceptColumns
     *
     * @return $this
     */
    public function setExceptColumns(null|string|array $exceptColumns): static
    {
        if (filled($exceptColumns)) {
            if (is_string($exceptColumns)) {
                $exceptColumns = explode(',', $exceptColumns);
            }

            $this->exceptColumns = $exceptColumns;
        }

        return $this;
    }

    /**
     * @param  string|array|null  $onlyColumns
     *
     * @return $this
     */
    public function setOnlyColumns(null|string|array $onlyColumns): static
    {
        if (filled($onlyColumns)) {
            if (is_string($onlyColumns)) {
                $onlyColumns = explode(',', $onlyColumns);
            }

            $this->onlyColumns = $onlyColumns;
        }

        return $this;
    }

    protected function serialize(Model $model): array
    {
        if (! $this->withHidden) {
            return $model->toArray();
        }

        $makeVisible = fn (Model $m) => $m->makeVisible($m->getHidden());

        return $model
            ->makeVisible($model->getHidden())
            ->setRelations(
                collect($model->getRelations())
                    ->mapWithKeys(fn ($relation, string $relationName) => [
                        $relationName => match (true) {
                            $relation instanceof Model => $makeVisible($relation),
                            $relation instanceof Collection => $relation->map($makeVisible),
                            default => $relation,
                        },
                    ])
                    ->all()
            )
            ->toArray();
    }
}
