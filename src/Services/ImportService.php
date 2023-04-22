<?php

namespace Vildanbina\ModelJson\Services;

use Arr;
use Vildanbina\ModelJson\Traits\ColumnManipulator;
use Vildanbina\ModelJson\Traits\EachClosure;
use Vildanbina\ModelJson\Traits\HasDestinationPath;
use Vildanbina\ModelJson\Traits\HasFilename;
use Vildanbina\ModelJson\Traits\HasModel;
use Vildanbina\ModelJson\Traits\ImportingWithRelationships;

/**
 * Class ImportService
 *
 * @package Vildanbina\ModelJson\Services
 */
class ImportService extends JsonService
{
    use HasModel;
    use HasFilename;
    use HasDestinationPath;
    use ColumnManipulator;
    use ImportingWithRelationships;
    use EachClosure;

    /**
     * @var bool
     */
    protected bool $updateWhenExists = false;
    /**
     * @var string|array|null
     */
    protected null|string|array $updateKeys = [];

    /**
     * @param  array|string|null  $updateKeys
     *
     * @return $this
     */
    public function updateKeys(null|array|string $updateKeys = []): ImportService
    {
        $this->updateKeys = $updateKeys;
        return $this;
    }

    /**
     * @param  bool  $updateWhenExists
     *
     * @return $this
     */
    public function updateWhenExists(bool $updateWhenExists = true): static
    {
        $this->updateWhenExists = $updateWhenExists;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalModelsCount(): int
    {
        return count($this->getJsonAsArray());
    }

    /**
     * @return bool|string
     */
    public function run(): bool|string
    {
        $data = $this->getJsonAsArray();

        Arr::map(Arr::wrap($data), function ($item) {
            $item = filled($this->onlyColumns) ?
                Arr::only($item, $this->onlyColumns) :
                Arr::except($item, $this->exceptColumns);

            if ($this->withoutTimestamps) {
                $item = Arr::except($item, static::$timestampsField);
            }

            if ($this->updateWhenExists) {
                $updateKeys = filled($this->updateKeys) ? $this->updateKeys : (new $this->model)->getKeyName();

                $model = $this->model::updateOrCreate(
                    Arr::only($item, $updateKeys),
                    Arr::except($item, $updateKeys),
                );
            } else {
                $model = $this->model::create($item);
            }

            $this->importRelationships($model, $item, $this->getRelationships());
        });

        return true;
    }
}
