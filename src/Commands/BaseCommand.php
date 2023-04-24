<?php

namespace Vildanbina\ModelJson\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Class BaseCommand
 *
 * @package Vildanbina\ModelJson\Commands
 */
abstract class BaseCommand extends Command
{
    /**
     * @return mixed
     */
    abstract public function handle();

    /**
     * @param  string  $model
     *
     * @return array|string
     */
    protected function qualifyModel(string $model): string
    {
        $model = ltrim($model, '\\/');

        $model = str_replace(DIRECTORY_SEPARATOR, '\\', $model);

        $rootNamespace = $this->laravel->getNamespace();

        $qualifiedModel = $model;

        if (!Str::startsWith($model, $rootNamespace)) {
            $modelNamespace = is_dir(app_path('Models')) ? 'Models' : '';
            $qualifiedModel = $rootNamespace . $modelNamespace . '\\' . $model;
        }

        return $qualifiedModel;
    }
}
