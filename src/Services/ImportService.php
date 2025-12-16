<?php

namespace Vildanbina\ModelJson\Services;

use Illuminate\Support\Arr;
use Vildanbina\ModelJson\Traits\ColumnManipulator;
use Vildanbina\ModelJson\Traits\DataManipulator;
use Vildanbina\ModelJson\Traits\EachClosure;
use Vildanbina\ModelJson\Traits\HasDestinationPath;
use Vildanbina\ModelJson\Traits\HasFilename;
use Vildanbina\ModelJson\Traits\HasModel;
use Vildanbina\ModelJson\Traits\ImportingWithRelationships;

/**
 * Class ImportService
 */
class ImportService extends JsonService
{
    use ColumnManipulator;
    use DataManipulator;
    use EachClosure;
    use HasDestinationPath;
    use HasFilename;
    use HasModel;
    use ImportingWithRelationships;

    protected bool $updateWhenExists = false;

    protected null|string|array $updateKeys = [];

    /**
     * @return $this
     */
    public function updateKeys(null|array|string $updateKeys = []): ImportService
    {
        $this->updateKeys = $updateKeys;

        return $this;
    }

    /**
     * @return $this
     */
    public function updateWhenExists(bool $updateWhenExists = true): static
    {
        $this->updateWhenExists = $updateWhenExists;

        return $this;
    }

    public function getTotalModelsCount(): int
    {
        return count($this->getJsonAsArray());
    }

    public function run(): bool|string
    {
        $data = $this->getJsonAsArray();

        Arr::map(Arr::wrap($data), function (array $item) {
            $item = filled($this->onlyColumns) ?
                Arr::only($item, $this->onlyColumns) :
                Arr::except($item, $this->exceptColumns);

            collect($this->forgetKeys)->each(function (string|array|int $key) use (&$item) {
                data_forget($item, $key);
            });

            if ($this->withoutTimestamps) {
                $item = Arr::except($item, static::$timestampsField);
            }

            $relationships = is_array($this->getRelationships()) ? $this->getRelationships() : explode('+', $this->getRelationships());
            $itemWithoutRelations = $this->getRelationships() !== null
                ? Arr::except($item, $relationships)
                : $item;

            if ($this->updateWhenExists) {
                $updateKeys = filled($this->updateKeys) ? $this->updateKeys : (new $this->model)->getKeyName();

                $model = $this->model::updateOrCreate(
                    Arr::only($itemWithoutRelations, $updateKeys),
                    Arr::except($itemWithoutRelations, $updateKeys),
                );
            } else {
                $model = $this->model::create($itemWithoutRelations);
            }

            $this->importRelationships($model, $item, $this->getRelationships());
        });

        return true;
    }
}
