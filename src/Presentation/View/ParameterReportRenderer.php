<?php

declare(strict_types=1);

namespace App\Presentation\View;

use App\Domain\Filter\Filter;
use App\Domain\Parameter\Parameter;
use App\Export\ExportPaths;

final class ParameterReportRenderer
{
    public function __construct(
        private readonly TemplateRenderer $templates
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
         *     filtersCsv: string,
         *     filtersJson: string,
         *     filtersXlsx: string,
         *     mappingCsv: string,
         *     mappingJson: string,
         *     mappingXlsx: string,
         *     datasetHash: string,
         *     sourceFile: string,
         *     fileName: string
         * } $data
         */
        $data = [
            'parameters' => $parameters,
            'filters' => $filters,

            'filtersCsv'  => "?download=filters&format=csv&_v={$hash}&_t={$token}",
            'filtersJson' => "?download=filters&format=json&_v={$hash}&_t={$token}",
            'filtersXlsx' => "?download=filters&format=xlsx&_v={$hash}&_t={$token}",

            'mappingCsv'  => "?download=mapping&format=csv&_v={$hash}&_t={$token}",
            'mappingJson' => "?download=mapping&format=json&_v={$hash}&_t={$token}",
            'mappingXlsx' => "?download=mapping&format=xlsx&_v={$hash}&_t={$token}",

            'datasetHash' => $hash,
            'sourceFile' => $sourceFile,
            'fileName' => basename($sourceFile),
        ];

        return $this->templates->render('report', $data);
    }
}
