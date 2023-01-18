<?php

namespace Vildanbina\ModelJson\Traits;

use File;
use Illuminate\Support\Facades\Storage;

/**
 * Trait HasDestinationPath
 *
 * @package Vildanbina\ModelJson\Traits
 */
trait HasDestinationPath
{
    /**
     * @var bool
     */
    protected bool $shouldSaveToPath = false;
    /**
     * @var string|null
     */
    protected null|string $savePath = null;

    /**
     * @param  string|null  $savePath
     * @param  bool         $shouldSaveToPath
     *
     * @return $this
     */
    public function shouldSaveToPath(null|string $savePath = null, bool $shouldSaveToPath = true): static
    {
        $this->savePath         = $savePath;
        $this->shouldSaveToPath = $shouldSaveToPath;

        return $this;
    }

    /**
     * @param  string  $fileName
     * @param  string  $jsonData
     *
     * @return string|bool
     */
    public function saveToDestination(string $fileName, string $jsonData): string|bool
    {
        if ($this->shouldSaveToPath) {
            File::put(
                $path = $this->getDestinationPath($fileName . '.json'),
                $jsonData
            );

            return $path;
        }

        return false;
    }

    /**
     * @param  string  $path
     *
     * @return string
     */
    protected function getDestinationPath(string $path = ''): string
    {
        return $this->savePath ? base_path($this->savePath . '\\' . $path) : Storage::path($path);
    }
}
