<?php

namespace Vildanbina\ModelJson\Traits;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

/**
 * Trait HasDestinationPath
 *
 * @package Vildanbina\ModelJson\Traits
 */
trait HasDestinationPath
{
    /**
     * @var string|null
     */
    protected null|string $path = null;

    /**
     * @param  string|null  $path
     *
     * @return $this
     */
    public function setPath(null|string $path = null): static
    {
        $this->path = $path;

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
        File::put(
            $path = $this->getDestinationPath($fileName . '.json'),
            $jsonData
        );

        return $path;
    }

    /**
     * @param  string  $path
     *
     * @return string
     */
    protected function getDestinationPath(string $path = ''): string
    {
        return $this->path ? base_path($this->path . (filled($path) ? '\\' . $path : '')) : Storage::path($path);
    }

    /**
     * @param  string  $path
     *
     * @return array
     */
    protected function getJsonAsArray(string $path = ''): array
    {
        return json_decode($this->getFileContent($path), true);
    }

    /**
     * @param  string  $path
     *
     * @return string
     * @throws FileNotFoundException
     */
    protected function getFileContent(string $path = ''): string
    {
        return File::get($path ?: $this->getDestinationPath());
    }
}
