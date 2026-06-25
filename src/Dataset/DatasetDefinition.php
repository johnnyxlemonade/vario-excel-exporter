<?php

declare(strict_types=1);

namespace App\Dataset;

final class DatasetDefinition
{
    public function __construct(
        private readonly string $key,
        private readonly string $labelKey,
        private readonly string $file,
        private readonly string $exportDirectory,
    ) {}

    public function key(): string
    {
        return $this->key;
    }

    public function labelKey(): string
    {
        return $this->labelKey;
    }

    public function file(): string
    {
        return $this->file;
    }

    public function exportDirectory(): string
    {
        return $this->exportDirectory;
    }
}
