<?php

namespace Vildanbina\ModelJson\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Vildanbina\ModelJson\Services\ImportService;

/**
 * Class ImportModelData
 *
 * @package Vildanbina\ModelJson\Commands
 */
class ImportModelData extends BaseCommand
{
    /**
     * @var string
     */
    protected $name = 'model:import';
    /**
     * @var string
     */
    protected $description = 'Import any Model\'s data from JSON file.';

    /**
     * @return int|mixed
     */
    public function handle()
    {
        $modelClass = $this->qualifyModel($this->argument('model'));

        $this->output->newLine();
        $this->output->write('Now, we are going to import your model (' . $modelClass . ') data from a JSON format.', true);
        $this->output->newLine();

        $exportService = ImportService::make()
            ->setModel($modelClass)
            ->setPath($this->argument('path'))
            ->setExceptColumns($this->option('except-fields'))
            ->setOnlyColumns($this->option('only-fields'))
            ->updateWhenExists($this->option('update-when-exists'))
            ->updateKeys($this->option('update-keys'))
            ->withoutTimestamps($this->option('without-timestamps'))
            ->onEach(function () {
                $this->output->progressAdvance();
            });

        $this->output->progressStart($exportService->getTotalModelsCount());

        $path = $exportService->run();
        $this->output->progressFinish();

        $this->output->success('Your JSON data has been successfully inserted to a database');

        return Command::SUCCESS;
    }

    /**
     * @return array[]
     */
    protected function getArguments(): array
    {
        return [
            ['model', null, InputOption::VALUE_REQUIRED, 'Model what you want to export into JSON'],
            ['path', null, InputOption::VALUE_REQUIRED, 'Model what you want to export into JSON'],
        ];
    }

    /**
     * @return array[]
     */
    protected function getOptions(): array
    {
        return [
            ['update-when-exists', null, InputOption::VALUE_NONE, 'Update existing records in the database.'],
            ['update-keys', null, InputOption::VALUE_OPTIONAL, 'Attributes of the model used to check if a record exists.'],
            ['except-fields', null, InputOption::VALUE_OPTIONAL, 'Columns that you do not want to save in the JSON file.'],
            ['only-fields', null, InputOption::VALUE_OPTIONAL, 'Only columns that you want to save in a JSON file.'],
            ['without-timestamps', null, InputOption::VALUE_NONE, 'Export without: created_at, updated_at and deleted_at columns'],
        ];
    }
}
