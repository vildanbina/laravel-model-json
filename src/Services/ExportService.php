<?php

namespace Vildanbina\ModelJson\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Vildanbina\ModelJson\Traits\ColumnManipulator;
use Vildanbina\ModelJson\Traits\DataManipulator;
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
    use DataManipulator;
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

        $this->modelQuery()->chunkMap(function (Model $model) use (&$data) {
            $value = filled($this->onlyColumns) ?
                Arr::only($this->serialize($model), $this->onlyColumns) :
                Arr::except($this->serialize($model), $this->exceptColumns);

            if ($this->withoutTimestamps) {
                $value = Arr::except($value, static::$timestampsField);
            }

            if (is_callable($this->onEach)) {
                value($this->onEach, $value);
            }

            collect($this->forgetKeys)->each(function (string|array|int $key) use (&$value) {
                data_forget($value, $key);
            });

            $data[] = $value;
        });

        $jsonData = json_encode($data, $this->beautifyJson ? JSON_PRETTY_PRINT : 0);
        $fileName = $this->getFilename();

        return $this->saveToDestination($fileName, $jsonData);
    }
}
