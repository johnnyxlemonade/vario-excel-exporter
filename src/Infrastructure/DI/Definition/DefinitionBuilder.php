<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Definition;

use App\Application\ExportService;
use App\Application\ParameterAnalyzer;
use App\Application\ParameterAnalyzerFactory;
use App\Application\ParameterProcessor;
use App\Application\ParameterRepository;
use App\Domain\Filter\FilterCollection;
use App\Domain\Filter\FilterExportConfig;
use App\Domain\Filter\ProductFilterExportConfig;
use App\Domain\Parameter\ParameterSnapshotLoader;
use App\Domain\Parameter\ParameterSnapshotWriter;
use App\Export\FilterExporter;
use App\Export\ProductFilterMapper;
use App\Infrastructure\DI\Definition\Expression\ArrayValue;
use App\Infrastructure\DI\Definition\Expression\ClassConstant;
use App\Infrastructure\DI\Definition\Expression\MethodCall;
use App\Infrastructure\DI\Definition\Expression\NewInstance;
use App\Infrastructure\DI\Definition\Expression\Parameter;
use App\Infrastructure\DI\Definition\Expression\Reference;
use App\Infrastructure\DI\Definition\Expression\ScalarValue;
use App\Infrastructure\DI\Definition\Expression\StaticCall;
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

final class DefinitionBuilder
{
    /**
     * @return list<ServiceDefinition>
     */
    public function build(): array
    {
        return [

            // --- simple services
            $this->service(Clock::class),
            $this->service(NdjsonReader::class),
            $this->service(NdjsonWriter::class),
            $this->service(ExcelReader::class),
            $this->service(RowWriterFactory::class),
            $this->service(FileHasher::class),
            $this->service(HtmlMinifier::class),

            // --- services with deps
            new ServiceDefinition(
                id: FileDownloader::class,
                returnType: FileDownloader::class,
                methodName: 'getFileDownloader',
                factory: new NewInstance(
                    FileDownloader::class,
                    [
                        new Argument(new Reference(RowWriterFactory::class)),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: Translator::class,
                returnType: Translator::class,
                methodName: 'getTranslator',
                factory: new NewInstance(
                    Translator::class,
                    [
                        new Argument(new Parameter('lang'), 'locale'),
                        new Argument(
                            new StaticCall(
                                className: 'self',
                                methodName: 'loadMessages',
                                arguments: [
                                    new Argument(new Parameter('lang')),
                                ],
                            ),
                            'messages'
                        ),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: TemplateRenderer::class,
                returnType: TemplateRenderer::class,
                methodName: 'getTemplateRenderer',
                factory: new NewInstance(
                    TemplateRenderer::class,
                    [
                        new Argument(new ClassConstant('self', 'TEMPLATE_DIR'), 'templateDir'),
                        new Argument(new Reference(Clock::class), 'clock'),
                        new Argument(new Reference(HtmlMinifier::class), 'minifier'),
                        new Argument(new Reference(Translator::class), 'translator'),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: ParameterReportRenderer::class,
                returnType: ParameterReportRenderer::class,
                methodName: 'getParameterReportRenderer',
                factory: new NewInstance(
                    ParameterReportRenderer::class,
                    [
                        new Argument(new Reference(TemplateRenderer::class)),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: DatasetSnapshotLoader::class,
                returnType: DatasetSnapshotLoader::class,
                methodName: 'getDatasetSnapshotLoader',
                factory: new NewInstance(
                    DatasetSnapshotLoader::class,
                    [
                        new Argument(new Reference(NdjsonReader::class)),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: DatasetSnapshotWriter::class,
                returnType: DatasetSnapshotWriter::class,
                methodName: 'getDatasetSnapshotWriter',
                factory: new NewInstance(
                    DatasetSnapshotWriter::class,
                    [
                        new Argument(new Reference(NdjsonWriter::class)),
                        new Argument(new Reference(Clock::class)),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: ParameterSnapshotLoader::class,
                returnType: ParameterSnapshotLoader::class,
                methodName: 'getParameterSnapshotLoader',
                factory: new NewInstance(
                    ParameterSnapshotLoader::class,
                    [
                        new Argument(new Reference(NdjsonReader::class)),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: ParameterSnapshotWriter::class,
                returnType: ParameterSnapshotWriter::class,
                methodName: 'getParameterSnapshotWriter',
                factory: new NewInstance(
                    ParameterSnapshotWriter::class,
                    [
                        new Argument(new Reference(NdjsonWriter::class)),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: FilterCollection::class,
                returnType: FilterCollection::class,
                methodName: 'getFilterCollection',
                factory: new NewInstance(
                    FilterCollection::class,
                    [
                        new Argument(new ArrayValue([])),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: FilterExporter::class,
                returnType: FilterExporter::class,
                methodName: 'getFilterExporter',
                factory: new NewInstance(
                    FilterExporter::class,
                    [
                        new Argument(
                            new NewInstance(
                                FilterExportConfig::class,
                                [
                                    new Argument(new ScalarValue('filter')),
                                    new Argument(new ScalarValue('value')),
                                ]
                            )
                        ),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: ProductFilterMapper::class,
                returnType: ProductFilterMapper::class,
                methodName: 'getProductFilterMapper',
                factory: new NewInstance(
                    ProductFilterMapper::class,
                    [
                        new Argument(
                            new NewInstance(
                                ProductFilterExportConfig::class,
                                [
                                    new Argument(new ScalarValue('product_code')),
                                    new Argument(new ScalarValue('filter')),
                                    new Argument(new ScalarValue('value')),
                                ]
                            )
                        ),
                    ]
                ),
            ),

            // --- factories
            $this->service(ParameterAnalyzerFactory::class),

            new ServiceDefinition(
                id: ParameterAnalyzer::class,
                returnType: ParameterAnalyzer::class,
                methodName: 'getParameterAnalyzer',
                factory: new MethodCall(
                    target: new Reference(ParameterAnalyzerFactory::class),
                    methodName: 'create',
                    arguments: [
                        new Argument(new Reference(FilterCollection::class)),
                    ],
                ),
            ),

            new ServiceDefinition(
                id: ParameterRepository::class,
                returnType: ParameterRepository::class,
                methodName: 'getParameterRepository',
                factory: new NewInstance(
                    ParameterRepository::class,
                    [
                        new Argument(new Reference(ExcelReader::class), 'reader'),
                        new Argument(new Reference(ParameterAnalyzer::class), 'analyzer'),
                        new Argument(new Reference(DatasetSnapshotLoader::class), 'datasetLoader'),
                        new Argument(new Reference(DatasetSnapshotWriter::class), 'datasetWriter'),
                        new Argument(new Reference(ParameterSnapshotLoader::class), 'parameterLoader'),
                        new Argument(new Reference(ParameterSnapshotWriter::class), 'parameterWriter'),
                        new Argument(new Reference(FileHasher::class), 'hasher'),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: ExportService::class,
                returnType: ExportService::class,
                methodName: 'getExportService',
                factory: new NewInstance(
                    ExportService::class,
                    [
                        new Argument(new Reference(FilterExporter::class), 'filterExporter'),
                        new Argument(new Reference(ProductFilterMapper::class), 'productFilterMapper'),
                        new Argument(new Reference(FileDownloader::class), 'downloader'),
                        new Argument(new Reference(Clock::class), 'clock'),
                        new Argument(new Reference(ParameterAnalyzer::class), 'analyzer'),
                        new Argument(new Reference(FileHasher::class), 'hasher'),
                    ]
                ),
            ),

            new ServiceDefinition(
                id: ParameterProcessor::class,
                returnType: ParameterProcessor::class,
                methodName: 'getParameterProcessor',
                factory: new NewInstance(
                    ParameterProcessor::class,
                    [
                        new Argument(new Reference(ParameterRepository::class), 'repository'),
                        new Argument(new Reference(ExportService::class), 'exportService'),
                        new Argument(new Reference(FilterCollection::class), 'filters'),
                        new Argument(new Reference(ParameterReportRenderer::class), 'reportRenderer'),
                    ]
                ),
            ),
        ];
    }

    private function service(string $class): ServiceDefinition
    {
        return new ServiceDefinition(
            id: $class,
            returnType: $class,
            methodName: 'get' . $this->short($class),
            factory: new NewInstance($class),
        );
    }

    private function short(string $fqcn): string
    {
        $pos = strrpos($fqcn, '\\');
        return $pos === false ? $fqcn : substr($fqcn, $pos + 1);
    }
}
