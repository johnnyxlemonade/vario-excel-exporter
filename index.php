<?php

declare(strict_types=1);

use App\Application\ProcessRequest;
use App\Domain\Export\ExportFormat;
use App\Domain\Export\ExportType;
use App\Infrastructure\DI\Container;
use App\Infrastructure\Http\QueryHelper;

require 'vendor/autoload.php';

$container = new Container();

try {

    $request = new ProcessRequest(
        file: 'export_vlastnosti_produktu.xlsx',
        exportDirectory: 'exports',
        format: ExportFormat::fromString(
            QueryHelper::get('format', 'xlsx')
        ),
        download: ExportType::fromQuery(
            QueryHelper::get('download')
        )
    );

    $container
        ->getParameterProcessor()
        ->process($request);

} catch (Throwable $e) {

    echo "[Error]: " . $e->getMessage();
}
