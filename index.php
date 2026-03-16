<?php

declare(strict_types=1);

use App\Application\ProcessRequest;
use App\Domain\Export\ExportFormat;
use App\Domain\Export\ExportType;
use App\Domain\Filter\Filter;
use App\Domain\Filter\FilterCollection;
use App\Infrastructure\DI\Container;
use App\Infrastructure\Http\QueryHelper;
use App\Presentation\View\Translator;

require 'vendor/autoload.php';

$container = new Container();

$lang = QueryHelper::get('lang', 'en');
$lang = in_array($lang, ['en', 'cs'], true) ? $lang : 'en';

/** @var array<string,string> $messages */
$messages = require __DIR__ . "/lang/$lang.php";

$container->set(
    Translator::class,
    new Translator(
        locale: $lang,
        messages: $messages
    )
);

$container->set(
    FilterCollection::class,
    new FilterCollection([
        new Filter('Hmotnost', 'weight'),
        new Filter('Tloušťka', 'thickness'),
        new Filter('Výška', 'height'),
        new Filter('Šířka', 'width'),
        new Filter('Délka', 'length'),
        new Filter('Výrobce', 'manufacturer'),
    ])
);

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
    echo '[Error]: ' . $e->getMessage();
}
