<?php

namespace Vildanbina\ModelJson\Traits;

/**
 * Trait HasFilename
 *
 * @package Vildanbina\ModelJson\Traits
 */
trait HasFilename
{
    /**
     * @var string|null
     */
    protected null|string $filename = null;

    /**
     * @return string|null
     */
    public function getFilename(): null|string
    {
        return $this->filename ?? $this->generateFilenameByModel();
    }

    /**
     * @param  string|null  $filename
     *
     * @return $this
     */
    public function setFilename(null|string $filename): static
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    protected function generateFilenameByModel(): string
    {
        return basename($this->model) . '-' . now()->format('Y-m-d-H-i-s');
    }
}
