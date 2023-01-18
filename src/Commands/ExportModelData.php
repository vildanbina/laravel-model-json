<?php

namespace Vildanbina\ModelJson\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Vildanbina\ModelJson\Services\ExportService;

/**
 * Class ExportModelData
 *
 * @package Vildanbina\ModelJson\Commands
 */
class ExportModelData extends Command
{
    /**
     * @var string
     */
    protected $name = 'model:export';
    /**
     * @var string
     */
    protected $description = 'Export any Model\'s data into JSON.';

    /**
     * @return int
     */
    public function handle()
    {
        $modelClass = $this->qualifyModel($this->argument('model'));

        $this->output->newLine();
        $this->output->write('Now, we are going to export your model (' . $modelClass . ') data into a JSON format.', true);
        $this->output->newLine();

        $exportService = ExportService::make()
            ->setModel($modelClass)
            ->setFilename($this->option('filename'))
            ->shouldSaveToPath($this->option('path'))
            ->setExceptColumns($this->option('except-fields'))
            ->setOnlyColumns($this->option('only-fields'))
            ->withoutTimestamps($this->option('without-timestamps'))
            ->onEach(function () {
                $this->output->progressAdvance();
            });

        $this->output->progressStart($exportService->getTotalModelsCount());

        $path = $exportService->run();
        $this->output->progressFinish();

        $this->output->success('Your model\'s JSON data has been saved to "' . $path . '"');

        return Command::SUCCESS;
    }

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

    /**
     * @return array[]
     */
    protected function getArguments(): array
    {
        return [
            ['model', null, InputOption::VALUE_REQUIRED, 'Model what you want to export into JSON'],
        ];
    }

    /**
     * @return array[]
     */
    protected function getOptions(): array
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'Path there to save the JSON data of the given model'],
            ['filename', null, InputOption::VALUE_OPTIONAL, 'Filename of JSON file'],
            ['except-fields', null, InputOption::VALUE_OPTIONAL, 'Columns that you do not want to save in the JSON file.'],
            ['only-fields', null, InputOption::VALUE_OPTIONAL, 'Only columns that you want to save in a JSON file.'],
            ['without-timestamps', null, InputOption::VALUE_NONE, 'Export without: created_at, updated_at and deleted_at columns'],
        ];
    }
}
