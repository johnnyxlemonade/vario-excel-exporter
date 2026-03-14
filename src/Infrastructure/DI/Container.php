<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

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

final class Container
{
    /** @var array<string, object> */
    private array $instances = [];

    /**
     * @template T of object
     * @param class-string<T>|string $key
     * @param callable(): T $factory
     * @return T
     */
    private function getShared(string $key, callable $factory): object
    {
        if (!isset($this->instances[$key])) {
            $this->instances[$key] = $factory();
        }

        /** @var T */
        return $this->instances[$key];
    }

    /*
    |--------------------------------------------------------------------------
    | Infrastructure (shared)
    |--------------------------------------------------------------------------
   */

    public function getClock(): Clock
    {
        return $this->getShared(Clock::class, fn() => new Clock());
    }

    public function getNdjsonReader(): NdjsonReader
    {
        return $this->getShared(NdjsonReader::class, fn() => new NdjsonReader());
    }

    public function getNdjsonWriter(): NdjsonWriter
    {
        return $this->getShared(NdjsonWriter::class, fn() => new NdjsonWriter());
    }

    public function getExcelReader(): ExcelReader
    {
        return $this->getShared(ExcelReader::class, fn() => new ExcelReader());
    }

    public function getFileDownloader(): FileDownloader
    {
        return $this->getShared(FileDownloader::class, fn() => new FileDownloader());
    }

    /*
    |--------------------------------------------------------------------------
    | Filters & Configs
    |--------------------------------------------------------------------------
   */
    public function getFilterCollection(): FilterCollection
    {
        return $this->getShared(FilterCollection::class, fn() => new FilterCollection([
            new Filter('Hmotnost', 'weight'),
            new Filter('Tloušťka', 'thickness'),
            new Filter('Výška', 'height'),
            new Filter('Šířka', 'width'),
            new Filter('Délka', 'length'),
            new Filter('Výrobce', 'manufacturer'),
        ]));
    }

    public function getFilterExporter(): FilterExporter
    {
        return new FilterExporter(new FilterExportConfig('filter', 'value'));
    }

    public function getProductFilterMapper(): ProductFilterMapper
    {
        return new ProductFilterMapper(new ProductFilterExportConfig('product_code', 'filter', 'value'));
    }

    public function getParameterAnalyzer(): ParameterAnalyzer
    {
        $filters = $this->getFilterCollection();
        return new ParameterAnalyzer($filters->names(), $filters->enabled());
    }

    /*
    |--------------------------------------------------------------------------
    | Snapshots & Presentation
    |--------------------------------------------------------------------------
   */

    public function getDatasetSnapshotLoader(): DatasetSnapshotLoader
    {
        return new DatasetSnapshotLoader($this->getNdjsonReader());
    }

    public function getDatasetSnapshotWriter(): DatasetSnapshotWriter
    {
        return new DatasetSnapshotWriter($this->getNdjsonWriter(), $this->getClock());
    }

    public function getParameterSnapshotLoader(): ParameterSnapshotLoader
    {
        return new ParameterSnapshotLoader($this->getNdjsonReader());
    }

    public function getParameterSnapshotWriter(): ParameterSnapshotWriter
    {
        return new ParameterSnapshotWriter($this->getNdjsonWriter());
    }

    public function getTemplateRenderer(): TemplateRenderer
    {
        return $this->getShared(TemplateRenderer::class, fn() => new TemplateRenderer(
            templateDir: __DIR__ . '/../../../templates',
            clock: $this->getClock(),
            minifier: new HtmlMinifier()
        ));
    }

    public function getParameterReportRenderer(): ParameterReportRenderer
    {
        return new ParameterReportRenderer($this->getTemplateRenderer());
    }

    /*
    |--------------------------------------------------------------------------
    | Application Root
    |--------------------------------------------------------------------------
   */
    public function getParameterProcessor(): ParameterProcessor
    {
        return $this->getShared(ParameterProcessor::class, fn() => new ParameterProcessor(
            reader: $this->getExcelReader(),
            analyzer: $this->getParameterAnalyzer(),
            filters: $this->getFilterCollection(),
            filterExporter: $this->getFilterExporter(),
            mapper: $this->getProductFilterMapper(),
            snapshotLoader: $this->getDatasetSnapshotLoader(),
            snapshotWriter: $this->getDatasetSnapshotWriter(),
            parameterSnapshotLoader: $this->getParameterSnapshotLoader(),
            parameterSnapshotWriter: $this->getParameterSnapshotWriter(),
            reportRenderer: $this->getParameterReportRenderer(),
            downloader: $this->getFileDownloader(),
            clock: $this->getClock()
        ));
    }
}
