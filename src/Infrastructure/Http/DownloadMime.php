<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

enum DownloadMime: string
{
    case CSV = 'text/csv; charset=utf-8';
    case JSON = 'application/json; charset=utf-8';
    case XML = 'application/xml; charset=utf-8';
    case XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    case TSV = 'text/tab-separated-values; charset=utf-8';
}
