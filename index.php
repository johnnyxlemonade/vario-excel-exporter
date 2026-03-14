<?php

declare(strict_types=1);

use App\Application\ProcessRequest;
use App\Domain\Export\ExportFormat;
use App\Infrastructure\DI\Container;
use App\Infrastructure\Http\QueryHelper;

require 'vendor/autoload.php';

$container = new Container();

try {

    $request = new ProcessRequest(
        file: 'export_vlastnosti_produktu.xlsx',
        exportDirectory: 'exports',
        format: ExportFormat::fromString(
            QueryHelper::get(key: 'format', default: 'xlsx')
        ),
        download: QueryHelper::get(key: 'download')
    );

    $container
        ->getParameterProcessor()
        ->process($request);

} catch (Throwable $e) {

    echo "[Error]: " . $e->getMessage();
}
