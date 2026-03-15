<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Domain\Export\ExportFormat;

enum DownloadMime: string
{
    case CSV = 'text/csv; charset=utf-8';
    case JSON = 'application/json; charset=utf-8';
    case XML = 'application/xml; charset=utf-8';
    case XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    case TSV = 'text/tab-separated-values; charset=utf-8';

    public static function fromFormat(ExportFormat $format): self
    {
        return match ($format) {
            ExportFormat::CSV  => self::CSV,
            ExportFormat::JSON => self::JSON,
            ExportFormat::XML  => self::XML,
            ExportFormat::XLSX => self::XLSX,
            ExportFormat::TSV  => self::TSV,
        };
    }
}
