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

        /** @var array{
         *     parameters: list<Parameter>,
         *     filters: list<Filter>,
         *     filtersCsv: non-falsy-string,
         *     filtersJson: non-falsy-string,
         *     filtersXlsx: non-falsy-string,
         *     filtersXml: non-falsy-string,
         *     filtersTsv: non-falsy-string,
         *     mappingCsv: non-falsy-string,
         *     mappingJson: non-falsy-string,
         *     mappingXlsx: non-falsy-string,
         *     mappingXml: non-falsy-string,
         *     mappingTsv: non-falsy-string,
         *     dataset: DatasetDefinition,
         *     datasets: list<DatasetDefinition>,
         *     datasetHash: string,
         *     sourceFile: string,
         *     fileName: string
         * } $data
         */
        $data = [
            'parameters' => $parameters,
            'filters' => $filters,

            'filtersCsv'  => '?download=filters&format=' . ExportFormat::CSV->value . "&_v={$hash}&_t={$token}",
            'filtersJson' => '?download=filters&format=' . ExportFormat::JSON->value . "&_v={$hash}&_t={$token}",
            'filtersXlsx' => '?download=filters&format=' . ExportFormat::XLSX->value . "&_v={$hash}&_t={$token}",
            'filtersXml'  => '?download=filters&format=' . ExportFormat::XML->value . "&_v={$hash}&_t={$token}",
            'filtersTsv'  => '?download=filters&format=' . ExportFormat::TSV->value . "&_v={$hash}&_t={$token}",

            'mappingCsv'  => '?download=mapping&format=' . ExportFormat::CSV->value . "&_v={$hash}&_t={$token}",
            'mappingJson' => '?download=mapping&format=' . ExportFormat::JSON->value . "&_v={$hash}&_t={$token}",
            'mappingXlsx' => '?download=mapping&format=' . ExportFormat::XLSX->value . "&_v={$hash}&_t={$token}",
            'mappingXml'  => '?download=mapping&format=' . ExportFormat::XML->value . "&_v={$hash}&_t={$token}",
            'mappingTsv'  => '?download=mapping&format=' . ExportFormat::TSV->value . "&_v={$hash}&_t={$token}",

            'dataset' => $this->dataset,
            'datasets' => $this->datasets->all(),

            'datasetHash' => $hash,
            'sourceFile' => $sourceFile,
            'fileName' => basename($sourceFile),
        ];

        return $this->templates->render('report', $data);
    }
}
