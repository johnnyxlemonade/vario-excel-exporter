<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Filter\FilterCollection;
use App\Presentation\View\ParameterReportRenderer;

final class ParameterProcessor
{
    public function __construct(
        private readonly ParameterRepository $repository,
        private readonly ExportService $exportService,
        private readonly FilterCollection $filters,
        private readonly ParameterReportRenderer $reportRenderer
    ) {}

    public function process(ProcessRequest $request): void
    {
        $dataset = $this->repository->dataset(
            file: $request->file(),
            dir: $request->exportDirectory(),
        );

        $parameters = $this->repository->parameters(
            dataset: $dataset,
            file: $request->file(),
            dir: $request->exportDirectory()
        );

        $paths = $this->exportService->paths(
            file: $request->file(),
            exportDir: $request->exportDirectory(),
        );

        if ($request->downloadFilters()) {
            $this->exportService->streamFilters(
                paths: $paths,
                format: $request->format(),
                parameters: $parameters,
            );
        }

        if ($request->downloadMapping()) {
            $this->exportService->streamMapping(
                paths: $paths,
                format: $request->format(),
                rows: $dataset->getRows(),
                parameters: $parameters
            );
        }

        echo $this->reportRenderer->render(
            parameters: $parameters,
            filters: $this->filters->all(),
            paths: $paths,
            sourceFile: $request->file()
        );
    }
}
