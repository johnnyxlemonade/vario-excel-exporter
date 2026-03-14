<?php

declare(strict_types=1);

use App\Infrastructure\DI\Container;

require 'vendor/autoload.php';

$container = new Container();

try {
    $container->getParameterProcessor()->process(
        file: 'export_vlastnosti_produktu.xlsx',
        filtersOutputDir: 'exports',
        mappingOutputDir: 'exports'
    );
} catch (Throwable $e) {
    echo "[Error]: " . $e->getMessage();
}
