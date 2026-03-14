<?php

declare(strict_types=1);

namespace App\Presentation\View;

use App\Domain\Parameter\Parameter;
use App\Export\ExportPaths;
use App\Infrastructure\Time\Clock;

final class ParameterReportRenderer
{
    public function __construct(
        private readonly TemplateRenderer $templates
    ) {
    }

    /**
     * @param list<Parameter> $parameters
     * @param array<string,mixed> $filters
     */
    public function render(
        array $parameters,
        array $filters,
        ExportPaths $paths,
        string $sourceFile
    ): string {

        /** @var array{
         *     parameters: list<Parameter>,
         *     filters: array<string,mixed>,
         *     filtersOutput: string,
         *     mappingOutput: string,
         *     datasetHash: string,
         *     sourceFile: string,
         *     fileName: string,
         *     clock: Clock
         * } $data
         */
        $data = [
            'parameters'    => $parameters,
            'filters'       => $filters,
            'filtersOutput' => $paths->getFilters(),
            'mappingOutput' => $paths->getMapping(),
            'datasetHash'   => $paths->getHash(),
            'sourceFile'    => $sourceFile,
            'fileName'      => basename($sourceFile),
        ];

        return $this->templates->render('report', $data);
    }
}
