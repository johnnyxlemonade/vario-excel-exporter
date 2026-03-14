<?php

declare(strict_types=1);


use App\Application\ParameterAnalyzer;
use App\Application\ParameterProcessor;
use App\Domain\Filter\Filter;
use App\Domain\Filter\FilterCollection;
use App\Domain\Filter\FilterExportConfig;
use App\Domain\Filter\ProductFilterExportConfig;
use App\Domain\Parameter\ParameterSnapshotLoader;
use App\Domain\Parameter\ParameterSnapshotWriter;
use App\Export\FilterExporter;
use App\Export\ProductFilterMapper;
use App\Infrastructure\DI\Container;
use App\Infrastructure\Excel\ExcelReader;
use App\Infrastructure\Http\FileDownloader;
use App\Infrastructure\IO\NdjsonReader;
use App\Infrastructure\IO\NdjsonWriter;
use App\Infrastructure\Snapshot\DatasetSnapshotLoader;
use App\Infrastructure\Snapshot\DatasetSnapshotWriter;
use App\Infrastructure\Time\Clock;
use App\Presentation\Html\HtmlMinifier;
use App\Presentation\View\ParameterReportRenderer;
use App\Presentation\View\TemplateRenderer;

require 'vendor/autoload.php';


//src
//│
//├─ Application
//│   ParameterProcessor.php
//│   ParameterAnalyzer.php
//│
//├─ Domain
//│   ├─ Dataset
//│   │   ExcelDataset.php
//│   │
//│   ├─ Parameter
//│   │   Parameter.php
//│   │   ParameterSnapshotLoader.php
//│   │   ParameterSnapshotWriter.php
//│   │
//│   └─ Filter
//│       Filter.php
//│       FilterCollection.php
//│       FilterExportConfig.php
//│       ProductFilterExportConfig.php
//│
//├─ Infrastructure
//│   ├─ Csv
//│   │   CsvWriter.php
//│   │
//│   ├─ Excel
//│   │   ExcelReader.php
//│   │
//│   ├─ Snapshot
//│   │   DatasetSnapshotLoader.php
//│   │   DatasetSnapshotWriter.php
//│   │
//│   ├─ IO
//│   │   NdjsonReader.php
//│   │   NdjsonWriter.php
//│   │
//│   ├─ Http
//│   │   FileDownloader.php
//│   │   InputNormalizer.php
//│   │   QueryHelper.php
//│   │
//│   └─ Time
//│       Clock.php
//│
//├─ Export
//│   FilterExporter.php
//│   ProductFilterMapper.php
//│   ExportPaths.php
//│
//└─ Presentation
//    ├─ Html
//    │   HtmlMinifier.php
//    │
//    └─ View
//        TemplateRenderer.php
//        ParameterReportRenderer.php


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


//try {
//
//    /*
//    |--------------------------------------------------------------------------
//    | Filters
//    |--------------------------------------------------------------------------
//    */
//
//    $filters = new FilterCollection([
//        new Filter('Hmotnost', 'weight'),
//        new Filter('Tloušťka', 'thickness'),
//        new Filter('Výška', 'height'),
//        new Filter('Šířka', 'width'),
//        new Filter('Délka', 'length'),
//        new Filter('Výrobce', 'manufacturer'),
//    ]);
//
//    /*
//    |--------------------------------------------------------------------------
//    | Export configuration
//    |--------------------------------------------------------------------------
//    */
//
//    $filterConfig = new FilterExportConfig(
//        filterColumn: 'filter',
//        valueColumn: 'value'
//    );
//
//    $productFilterConfig = new ProductFilterExportConfig(
//        productCodeColumn: 'product_code',
//        filterColumn: 'filter',
//        valueColumn: 'value'
//    );
//
//    /*
//    |--------------------------------------------------------------------------
//    | Services
//    |--------------------------------------------------------------------------
//    */
//
//    $clock = new Clock();
//    $ndjsonReader = new NdjsonReader();
//    $ndjsonWriter = new NdjsonWriter();
//    $parameterAnalyzer = new ParameterAnalyzer(
//        ignore: $filters->names(),
//        enabled: $filters->enabled()
//    );
//
//    $processor = new ParameterProcessor(
//        reader: new ExcelReader(),
//        analyzer: $parameterAnalyzer,
//        filters: $filters,
//        filterExporter: new FilterExporter(
//            config: $filterConfig
//        ),
//        mapper: new ProductFilterMapper(
//            config: $productFilterConfig
//        ),
//        snapshotLoader: new DatasetSnapshotLoader(
//            reader: $ndjsonReader,
//        ),
//        snapshotWriter: new DatasetSnapshotWriter(
//            writer: $ndjsonWriter,
//            clock: $clock
//        ),
//        parameterSnapshotLoader: new ParameterSnapshotLoader(
//            reader: $ndjsonReader
//        ),
//        parameterSnapshotWriter: new ParameterSnapshotWriter(
//            writer: $ndjsonWriter,
//        ),
//        reportRenderer: new ParameterReportRenderer(
//            templates: new TemplateRenderer(
//                templateDir: __DIR__ . '/templates',
//                minifier: new HtmlMinifier()
//            )
//        ),
//        downloader: new FileDownloader(),
//        clock: $clock
//    );
//
//    /*
//    |--------------------------------------------------------------------------
//    | Run
//    |--------------------------------------------------------------------------
//    */
//
//    $processor->process(
//        file: 'export_vlastnosti_produktu.xlsx',
//        filtersOutputDir: 'exports',
//        mappingOutputDir: 'exports'
//    );
//
//} catch (Throwable $e) {
//
//    echo $e->getMessage();
//    exit;
//
//}
