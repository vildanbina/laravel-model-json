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
    protected function qualifyModel(string $model): array|string
    {
        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->laravel->getNamespace();

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        return is_dir(app_path('Models'))
            ? $rootNamespace . 'Models\\' . $model
            : $rootNamespace . $model;
    }
}
