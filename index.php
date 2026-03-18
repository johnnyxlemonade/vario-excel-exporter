<?php

declare(strict_types=1);

use App\Application\ProcessRequest;
use App\Domain\Export\ExportFormat;
use App\Domain\Export\ExportType;
use App\Domain\Filter\Filter;
use App\Domain\Filter\FilterCollection;
use App\Infrastructure\DI\ContainerFactory;
use App\Infrastructure\Http\QueryHelper;

require __DIR__ . '/vendor/autoload.php';

$container = (new ContainerFactory())->create();

$lang = QueryHelper::get('lang', 'en');
$lang = in_array($lang, ['en', 'cs'], true) ? $lang : 'en';

$container = (new ContainerFactory())->create([
    'lang' => $lang,
]);

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
