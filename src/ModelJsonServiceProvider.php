<?php

namespace Vildanbina\ModelJson;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vildanbina\ModelJson\Commands\ExportModelData;
use Vildanbina\ModelJson\Commands\ImportModelData;

/**
 * Class ModelJsonServiceProvider
 *
 * @package Vildanbina\ModelJson
 */
class ModelJsonServiceProvider extends PackageServiceProvider
{
    /**
     * @param  Package  $package
     *
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-model-json')
            ->hasCommands([
                ExportModelData::class,
                ImportModelData::class,
            ]);
    }
}
