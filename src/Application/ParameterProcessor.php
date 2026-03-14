<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Dataset\ExcelDataset;
use App\Domain\Filter\FilterCollection;
use App\Domain\Parameter\Parameter;
use App\Domain\Parameter\ParameterSnapshotLoader;
use App\Domain\Parameter\ParameterSnapshotWriter;
use App\Export\ExportPaths;
use App\Export\FilterExporter;
use App\Export\ProductFilterMapper;
use App\Infrastructure\Excel\ExcelReader;
use App\Infrastructure\Http\FileDownloader;
use App\Infrastructure\Http\QueryHelper;
use App\Infrastructure\Snapshot\DatasetSnapshotLoader;
use App\Infrastructure\Snapshot\DatasetSnapshotWriter;
use App\Infrastructure\Time\Clock;
use App\Presentation\View\ParameterReportRenderer;

class ParameterProcessor
{
    public function __construct(
        /* Domain */
        private readonly ExcelReader $reader,
        private readonly ParameterAnalyzer $analyzer,
        private readonly FilterCollection $filters,

        /* Export */
        private readonly FilterExporter $filterExporter,
        private readonly ProductFilterMapper $mapper,

        /* Snapshot persistence */
        private readonly DatasetSnapshotLoader $snapshotLoader,
        private readonly DatasetSnapshotWriter $snapshotWriter,
        private readonly ParameterSnapshotLoader $parameterSnapshotLoader,
        private readonly ParameterSnapshotWriter $parameterSnapshotWriter,

        /* Output */
        private readonly ParameterReportRenderer $reportRenderer,

        /* Infrastructure */
        private readonly FileDownloader $downloader,
        private readonly Clock $clock
    ) {
    }

    public function process(
        string $file,
        string $filtersOutputDir,
        string $mappingOutputDir
    ): void {

        $paths = $this->resolveExportPaths(
            $file,
            $filtersOutputDir,
            $mappingOutputDir
        );

        $dataset = $this->loadDatasetFromSnapshotOrExcel(
            file: $file,
            filtersOutputDir: $filtersOutputDir
        );

        $parameters = $this->loadParametersFromSnapshotOrAnalyze(
            dataset: $dataset,
            file: $file,
            filtersOutputDir: $filtersOutputDir
        );

        /* -------------------------------------------------
           Lazy download export (STREAM CSV)
        --------------------------------------------------*/

        $download = QueryHelper::get('download');
        $format = QueryHelper::get('format', 'xlsx');

        if ($download === 'filters') {
            $this->streamFilters(paths: $paths, parameters: $parameters, format: $format);
        }

        if ($download === 'mapping') {
            $this->streamMapping(paths: $paths, dataset: $dataset, parameters: $parameters, format: $format);
        }

        /* ----------------------------------------
           HTML REPORT
        -----------------------------------------*/

        echo $this->reportRenderer->render(
            parameters: $parameters,
            filters: $this->filters->all(),
            paths: $paths,
            sourceFile: $file
        );
    }

    private function buildExportFilename(
        ExportPaths $paths,
        string $type,
        string $extension = 'xlsx',
    ): string {
        return sprintf(
            '%s_%s_%s.%s',
            $type,
            $paths->getHash(),
            $this->clock->exportTimestamp(),
            $extension
        );
    }

    private function streamFilters(ExportPaths $paths, array $parameters, string $format): never
    {
        $filename = $this->buildExportFilename(paths: $paths, type: 'filters', extension: $format);

        $callback = fn($writeRow) => $this->filterExporter->export($parameters, $writeRow);

        match ($format) {
            'csv'  => $this->downloader->streamCsv($filename, $callback),
            'json' => $this->downloader->streamJson($filename, $callback),
            default => $this->downloader->streamExcel($filename, $callback),
        };
    }

    private function streamMapping(
        ExportPaths $paths,
        ExcelDataset $dataset,
        array $parameters,
        string $format
    ): never {
        $filename = $this->buildExportFilename(paths: $paths, type: 'mapping', extension: $format);

        $callback = fn($writeRow) => $this->mapper->export(
            $parameters,
            $dataset->getRows(),
            $writeRow
        );

        match ($format) {
            'csv'  => $this->downloader->streamCsv($filename, $callback),
            'json' => $this->downloader->streamJson($filename, $callback),
            default => $this->downloader->streamExcel($filename, $callback),
        };
    }


    private function loadDatasetFromSnapshotOrExcel(
        string $file,
        string $filtersOutputDir
    ): ExcelDataset {

        $snapshotFile = rtrim($filtersOutputDir, '/')
            . '/dataset_' . substr(sha1_file($file), 0, 12) . '.json';

        if (file_exists($snapshotFile)) {
            return $this->snapshotLoader->load($snapshotFile);
        }

        $dataset = $this->loadDataset($file);

        $this->snapshotWriter->write(
            $dataset,
            $snapshotFile
        );

        return $dataset;
    }

    private function loadParametersFromSnapshotOrAnalyze(
        ExcelDataset $dataset,
        string $file,
        string $filtersOutputDir
    ): array {

        $datasetHash = substr(sha1_file($file), 0, 12);
        $configHash = $this->analyzer->getConfigHash();

        $parameterSnapshot = rtrim($filtersOutputDir, '/')
            . "/parameters_{$datasetHash}_{$configHash}.json";

        if (file_exists($parameterSnapshot)) {
            return $this->parameterSnapshotLoader->load($parameterSnapshot);
        }

        $parameters = $this->analyzeParameters($dataset);

        $this->parameterSnapshotWriter->write(
            $parameters,
            $parameterSnapshot
        );

        return $parameters;
    }

    private function loadDataset(string $file): ExcelDataset
    {
        return $this->reader->read($file);
    }

    /**
     * @return list<Parameter>
     */
    private function analyzeParameters(ExcelDataset $dataset): array
    {
        return $this->analyzer->analyze(
            $dataset->getHeaders(),
            $dataset->getLabels(),
            $dataset->getRows()
        );
    }

    private function resolveExportPaths(
        string $file,
        string $filtersOutputDir,
        string $mappingOutputDir
    ): ExportPaths {

        $configHash = $this->analyzer->getConfigHash();

        $hash = substr(
            sha1(sha1_file($file) . $configHash),
            0,
            12
        );

        $filtersOutput = rtrim($filtersOutputDir, '/') . "/filters_{$hash}.csv";
        $mappingOutput = rtrim($mappingOutputDir, '/') . "/mapping_{$hash}.csv";

        return new ExportPaths(
            $filtersOutput,
            $mappingOutput,
            $hash
        );
    }
}
