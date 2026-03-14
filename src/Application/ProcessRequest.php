<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Export\ExportFormat;
use App\Domain\Export\ExportType;

final class ProcessRequest
{
    public function __construct(
        private readonly string $file,
        private readonly string $exportDirectory,
        private readonly ExportFormat $format,
        private readonly ?ExportType $download,
    ) {}

    public function file(): string
    {
        return $this->file;
    }

    public function exportDirectory(): string
    {
        return $this->exportDirectory;
    }

    public function format(): ExportFormat
    {
        return $this->format;
    }

    public function download(): ?ExportType
    {
        return $this->download;
    }

    public function downloadFilters(): bool
    {
        return $this->download === ExportType::FILTERS;
    }

    public function downloadMapping(): bool
    {
        return $this->download === ExportType::MAPPING;
    }
}
