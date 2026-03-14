<?php

declare(strict_types=1);

namespace App\Domain\Export;

enum ExportFormat: string
{
    case CSV = 'csv';
    case JSON = 'json';
    case XLSX = 'xlsx';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::XLSX;
    }
}
