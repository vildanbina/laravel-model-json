<?php

namespace Vildanbina\ModelJson\Services;

use Arr;
use Vildanbina\ModelJson\Traits\ColumnManipulator;
use Vildanbina\ModelJson\Traits\EachClosure;
use Vildanbina\ModelJson\Traits\HasDestinationPath;
use Vildanbina\ModelJson\Traits\HasFilename;
use Vildanbina\ModelJson\Traits\HasModel;
use Vildanbina\ModelJson\Traits\HasRelationships;

/**
 * Class ExportService
 *
 * @package Vildanbina\ModelJson\Services
 */
class ExportService extends JsonService
{
    use HasModel;
    use HasFilename;
    use HasDestinationPath;
    use ColumnManipulator;
    use HasRelationships;
    use EachClosure;

    /**
     * @var bool
     */
    protected bool $beautifyJson = false;

    /**
     * @param  bool  $beautifyJson
     *
     * @return $this
     */
    public function beautifyJson(bool $beautifyJson): static
    {
        $this->beautifyJson = $beautifyJson;
        return $this;
    }

    /**
     * @return bool|string
     */
    public function run(): bool|string
    {
        $data = [];

        $this->modelQuery()->chunkMap(function ($model) use (&$data) {
            $value = filled($this->onlyColumns) ?
                Arr::only($model->toArray(), $this->onlyColumns) :
                Arr::except($model->toArray(), $this->exceptColumns);

            if ($this->withoutTimestamps) {
                $value = Arr::except($value, static::$timestampsField);
            }

            if (is_callable($this->onEach)) {
                value($this->onEach, $value);
            }

            $data[] = $value;
        });

        $jsonData = json_encode($data, $this->beautifyJson ? JSON_PRETTY_PRINT : 0);
        $fileName = $this->getFilename();

        return $this->saveToDestination($fileName, $jsonData);
    }
}
