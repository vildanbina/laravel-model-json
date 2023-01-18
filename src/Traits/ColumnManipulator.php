<?php

namespace Vildanbina\ModelJson\Traits;

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
     * @var array
     */
    protected array $exceptColumns = [];
    /**
     * @var array
     */
    protected array $onlyColumns = [];

    /**
     * @param  bool|null  $withoutTimestamps
     *
     * @return $this
     */
    public function withoutTimestamps(null|bool $withoutTimestamps = true): static
    {
        $this->withoutTimestamps = boolval($withoutTimestamps);
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
}
