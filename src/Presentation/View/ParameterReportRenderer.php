<?php

declare(strict_types=1);

namespace App\Presentation\View;

use App\Dataset\DatasetCollection;
use App\Dataset\DatasetDefinition;
use App\Domain\Export\ExportFormat;
use App\Domain\Filter\Filter;
use App\Domain\Parameter\Parameter;
use App\Export\ExportPaths;

final class ParameterReportRenderer
{
    public function __construct(
        private readonly TemplateRenderer $templates,
        private readonly DatasetDefinition $dataset,
        private readonly DatasetCollection $datasets,
    ) {}

    /**
     * @param list<Parameter> $parameters
     * @param list<Filter> $filters
     */
    public function render(
        array $parameters,
        array $filters,
        ExportPaths $paths,
        string $sourceFile
    ): string {
        $hash = $paths->getHash();
        $token = time();

        $data = [
            'parameters' => $parameters,
            'filters' => $filters,

            'filtersCsv' => $this->buildExportUrl(
                download: 'filters',
                format: ExportFormat::CSV,
                hash: $hash,
                token: $token,
            ),
            'filtersJson' => $this->buildExportUrl(
                download: 'filters',
                format: ExportFormat::JSON,
                hash: $hash,
                token: $token,
            ),
            'filtersXlsx' => $this->buildExportUrl(
                download: 'filters',
                format: ExportFormat::XLSX,
                hash: $hash,
                token: $token,
            ),
            'filtersXml' => $this->buildExportUrl(
                download: 'filters',
                format: ExportFormat::XML,
                hash: $hash,
                token: $token,
            ),
            'filtersTsv' => $this->buildExportUrl(
                download: 'filters',
                format: ExportFormat::TSV,
                hash: $hash,
                token: $token,
            ),

            'mappingCsv' => $this->buildExportUrl(
                download: 'mapping',
                format: ExportFormat::CSV,
                hash: $hash,
                token: $token,
            ),
            'mappingJson' => $this->buildExportUrl(
                download: 'mapping',
                format: ExportFormat::JSON,
                hash: $hash,
                token: $token,
            ),
            'mappingXlsx' => $this->buildExportUrl(
                download: 'mapping',
                format: ExportFormat::XLSX,
                hash: $hash,
                token: $token,
            ),
            'mappingXml' => $this->buildExportUrl(
                download: 'mapping',
                format: ExportFormat::XML,
                hash: $hash,
                token: $token,
            ),
            'mappingTsv' => $this->buildExportUrl(
                download: 'mapping',
                format: ExportFormat::TSV,
                hash: $hash,
                token: $token,
            ),

            'dataset' => $this->dataset,
            'datasets' => $this->datasets->all(),

            'datasetHash' => $hash,
            'sourceFile' => $sourceFile,
            'fileName' => basename($sourceFile),
        ];

        return $this->templates->render('report', $data);
    }

    private function buildExportUrl(
        string $download,
        ExportFormat $format,
        string $hash,
        int $token,
    ): string {
        return '?' . http_build_query([
                'lang' => $this->templates->locale(),
                'dataset' => $this->dataset->key(),
                'download' => $download,
                'format' => $format->value,
                '_v' => $hash,
                '_t' => $token,
            ]);
    }
}
