<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use App\Application\ExportService;
use App\Application\ParameterAnalyzer;
use App\Application\ParameterProcessor;
use App\Application\ParameterRepository;
use App\Domain\Filter\FilterCollection;
use App\Domain\Filter\FilterExportConfig;
use App\Domain\Filter\ProductFilterExportConfig;
use App\Domain\Parameter\ParameterSnapshotLoader;
use App\Domain\Parameter\ParameterSnapshotWriter;
use App\Export\FilterExporter;
use App\Export\ProductFilterMapper;
use App\Infrastructure\Excel\ExcelReader;
use App\Infrastructure\Export\RowWriterFactory;
use App\Infrastructure\Hash\FileHasher;
use App\Infrastructure\Http\FileDownloader;
use App\Infrastructure\IO\NdjsonReader;
use App\Infrastructure\IO\NdjsonWriter;
use App\Infrastructure\Snapshot\DatasetSnapshotLoader;
use App\Infrastructure\Snapshot\DatasetSnapshotWriter;
use App\Infrastructure\Time\Clock;
use App\Presentation\Html\HtmlMinifier;
use App\Presentation\View\ParameterReportRenderer;
use App\Presentation\View\TemplateRenderer;
use App\Presentation\View\Translator;

final class Container
{
    private const TEMPLATE_DIR = __DIR__ . '/../../../templates';

    private const LANG_DIR = __DIR__ . '/../../../lang';

    /** @var array<string,object> */
    private array $instances = [];

    /** @var array<string,callable(self):object> */
    private array $factories;

    public function __construct()
    {
        $this->factories = [

            Clock::class => fn(self $c) => new Clock(),

            NdjsonReader::class => fn(self $c) => new NdjsonReader(),

            NdjsonWriter::class => fn(self $c) => new NdjsonWriter(),

            ExcelReader::class => fn(self $c) => new ExcelReader(),

            RowWriterFactory::class => fn(self $c) => new RowWriterFactory(),

            FileDownloader::class => fn(self $c) =>
            new FileDownloader(
                $c->getRowWriterFactory()
            ),

            FileHasher::class => fn(self $c) => new FileHasher(),

            HtmlMinifier::class => fn(self $c) => new HtmlMinifier(),

            Translator::class => fn(self $c) => new Translator(
                locale: 'en',
                messages: self::loadMessages('en')
            ),

            TemplateRenderer::class => fn(self $c) => new TemplateRenderer(
                templateDir: self::TEMPLATE_DIR,
                clock: $c->getClock(),
                minifier: $c->getHtmlMinifier(),
                translator: $c->getTranslator()
            ),

            ParameterReportRenderer::class => fn(self $c) =>
            new ParameterReportRenderer($c->getTemplateRenderer()),

            DatasetSnapshotLoader::class => fn(self $c) =>
            new DatasetSnapshotLoader($c->getNdjsonReader()),

            DatasetSnapshotWriter::class => fn(self $c) =>
            new DatasetSnapshotWriter($c->getNdjsonWriter(), $c->getClock()),

            ParameterSnapshotLoader::class => fn(self $c) =>
            new ParameterSnapshotLoader($c->getNdjsonReader()),

            ParameterSnapshotWriter::class => fn(self $c) =>
            new ParameterSnapshotWriter($c->getNdjsonWriter()),

            FilterCollection::class => fn(self $c) => new FilterCollection([]),

            FilterExporter::class => fn(self $c) =>
            new FilterExporter(new FilterExportConfig('filter', 'value')),

            ProductFilterMapper::class => fn(self $c) =>
            new ProductFilterMapper(
                new ProductFilterExportConfig('product_code', 'filter', 'value')
            ),

            ParameterRepository::class => fn(self $c) =>
            new ParameterRepository(
                reader: $c->getExcelReader(),
                analyzer: $c->getParameterAnalyzer(),
                datasetLoader: $c->getDatasetSnapshotLoader(),
                datasetWriter: $c->getDatasetSnapshotWriter(),
                parameterLoader: $c->getParameterSnapshotLoader(),
                parameterWriter: $c->getParameterSnapshotWriter(),
                hasher: $c->getFileHasher(),
            ),

            ExportService::class => fn(self $c) =>
            new ExportService(
                filterExporter: $c->getFilterExporter(),
                productFilterMapper: $c->getProductFilterMapper(),
                downloader: $c->getFileDownloader(),
                clock: $c->getClock(),
                analyzer: $c->getParameterAnalyzer(),
                hasher: $c->getFileHasher(),
            ),

            ParameterAnalyzer::class => function (self $c) {

                $filters = $c->getFilterCollection();

                return new ParameterAnalyzer(
                    $filters->names(),
                    $filters->enabled()
                );
            },

            ParameterProcessor::class => fn(self $c) =>
            new ParameterProcessor(
                repository: $c->getParameterRepository(),
                exportService: $c->getExportService(),
                filters: $c->getFilterCollection(),
                reportRenderer: $c->getParameterReportRenderer(),
            ),

        ];
    }

    public function set(string $id, object $service): void
    {
        $this->instances[$id] = $service;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    private function service(string $id): object
    {
        if (!isset($this->instances[$id])) {
            $this->instances[$id] = ($this->factories[$id])($this);
        }

        /** @var T */
        return $this->instances[$id];
    }

    public function getClock(): Clock
    {
        return $this->service(Clock::class);
    }

    public function getNdjsonReader(): NdjsonReader
    {
        return $this->service(NdjsonReader::class);
    }

    public function getNdjsonWriter(): NdjsonWriter
    {
        return $this->service(NdjsonWriter::class);
    }

    public function getExcelReader(): ExcelReader
    {
        return $this->service(ExcelReader::class);
    }

    public function getRowWriterFactory(): RowWriterFactory
    {
        return $this->service(RowWriterFactory::class);
    }

    public function getFileDownloader(): FileDownloader
    {
        return $this->service(FileDownloader::class);
    }

    public function getFilterCollection(): FilterCollection
    {
        return $this->service(FilterCollection::class);
    }

    public function getFilterExporter(): FilterExporter
    {
        return $this->service(FilterExporter::class);
    }

    public function getProductFilterMapper(): ProductFilterMapper
    {
        return $this->service(ProductFilterMapper::class);
    }

    public function getParameterAnalyzer(): ParameterAnalyzer
    {
        return $this->service(ParameterAnalyzer::class);
    }

    public function getDatasetSnapshotLoader(): DatasetSnapshotLoader
    {
        return $this->service(DatasetSnapshotLoader::class);
    }

    public function getDatasetSnapshotWriter(): DatasetSnapshotWriter
    {
        return $this->service(DatasetSnapshotWriter::class);
    }

    public function getParameterSnapshotLoader(): ParameterSnapshotLoader
    {
        return $this->service(ParameterSnapshotLoader::class);
    }

    public function getParameterSnapshotWriter(): ParameterSnapshotWriter
    {
        return $this->service(ParameterSnapshotWriter::class);
    }

    public function getTemplateRenderer(): TemplateRenderer
    {
        return $this->service(TemplateRenderer::class);
    }

    public function getHtmlMinifier(): HtmlMinifier
    {
        return $this->service(HtmlMinifier::class);
    }

    public function getParameterReportRenderer(): ParameterReportRenderer
    {
        return $this->service(ParameterReportRenderer::class);
    }

    public function getFileHasher(): FileHasher
    {
        return $this->service(FileHasher::class);
    }

    public function getParameterProcessor(): ParameterProcessor
    {
        return $this->service(ParameterProcessor::class);
    }

    public function getParameterRepository(): ParameterRepository
    {
        return $this->service(ParameterRepository::class);
    }

    public function getExportService(): ExportService
    {
        return $this->service(ExportService::class);
    }

    public function getTranslator(): Translator
    {
        return $this->service(Translator::class);
    }

    /**
     * @return array<string,string>
     */
    private static function loadMessages(string $locale): array
    {
        $file = self::LANG_DIR . '/' . $locale . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException("Language file not found: {$file}");
        }

        $messages = require $file;

        if (!is_array($messages)) {
            throw new \RuntimeException('Language file must return array.');
        }

        /** @var array<string,string> $messages */
        return $messages;
    }
}
