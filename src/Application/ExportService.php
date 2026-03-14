<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Export\ExportFormat;
use App\Domain\Parameter\Parameter;
use App\Export\ExportPaths;
use App\Export\FilterExporter;
use App\Export\ProductFilterMapper;
use App\Infrastructure\Hash\FileHasher;
use App\Infrastructure\Http\FileDownloader;
use App\Infrastructure\Time\Clock;

final class ExportService
{
    public function __construct(
        private readonly FilterExporter $filterExporter,
        private readonly ProductFilterMapper $productFilterMapper,
        private readonly FileDownloader $downloader,
        private readonly Clock $clock,
        private readonly ParameterAnalyzer $analyzer,
        private readonly FileHasher $hasher
    ) {}

    public function paths(
        string $file,
        string $exportDir
    ): ExportPaths {

        $configHash = $this->analyzer->getConfigHash();
        $fileHash = $this->hasher->sha1($file);

        $hash = $this->hasher->combinedShort(
            $fileHash,
            $configHash
        );

        return new ExportPaths(
            dir: rtrim($exportDir, '/'),
            hash: $hash
        );
    }

    /**
     * @param list<Parameter> $parameters
     */
    public function streamFilters(
        ExportPaths $paths,
        ExportFormat $format,
        array $parameters,
    ): never {

        $filename = sprintf(
            '%s/filters_%s_%s.%s',
            $paths->getDir(),
            $paths->getHash(),
            $this->clock->exportTimestamp(),
            $format->value
        );

        $callback = fn(callable $writeRow)
        => $this->filterExporter->export($parameters, $writeRow);

        match ($format) {
            ExportFormat::CSV  => $this->downloader->streamCsv($filename, $callback),
            ExportFormat::JSON => $this->downloader->streamJson($filename, $callback),
            ExportFormat::XLSX => $this->downloader->streamExcel($filename, $callback),
        };
    }

    /**
     * @param list<Parameter> $parameters
     * @param iterable<int, array<int, mixed>> $rows
     */
    public function streamMapping(
        ExportPaths $paths,
        ExportFormat $format,
        iterable $rows,
        array $parameters,
    ): never {

        $filename = sprintf(
            '%s/mapping_%s_%s.%s',
            $paths->getDir(),
            $paths->getHash(),
            $this->clock->exportTimestamp(),
            $format->value
        );

        $callback = fn(callable $writeRow) =>
        $this->productFilterMapper->export(
            $parameters,
            $rows,
            $writeRow
        );

        match ($format) {
            ExportFormat::CSV  => $this->downloader->streamCsv($filename, $callback),
            ExportFormat::JSON => $this->downloader->streamJson($filename, $callback),
            ExportFormat::XLSX => $this->downloader->streamExcel($filename, $callback),
        };
    }
}
