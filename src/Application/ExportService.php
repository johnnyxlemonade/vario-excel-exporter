<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Export\ExportFormat;
use App\Domain\Parameter\Parameter;
use App\Export\ExportPaths;
use App\Export\FilterExporter;
use App\Export\ProductFilterMapper;
use App\Export\RowWriter;
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

        $callback = fn(RowWriter $writer) =>
        $this->filterExporter->export($parameters, $writer);

        $headers = $this->filterExporter->config()->headers();

        $this->stream($paths, $format, 'filters', $headers, $callback);
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

        $callback = fn(RowWriter $writer) =>
        $this->productFilterMapper->export(
            $parameters,
            $rows,
            $writer
        );

        $headers = $this->productFilterMapper->config()->headers();

        $this->stream($paths, $format, 'mapping', $headers, $callback);
    }

    /**
     * @param list<string> $headers
     * @param callable(RowWriter):void $callback
     */
    private function stream(
        ExportPaths $paths,
        ExportFormat $format,
        string $prefix,
        array $headers,
        callable $callback
    ): never {

        $filename = sprintf(
            '%s/%s_%s_%s.%s',
            $paths->getDir(),
            $prefix,
            $paths->getHash(),
            $this->clock->exportTimestamp(),
            $format->value
        );

        match ($format) {
            ExportFormat::CSV  => $this->downloader->streamCsv(filename: $filename, writerCallback: $callback),
            ExportFormat::JSON => $this->downloader->streamJson(filename: $filename, headers: $headers, writerCallback: $callback),
            ExportFormat::XLSX => $this->downloader->streamExcel(filename: $filename, writerCallback: $callback),
            ExportFormat::XML  => $this->downloader->streamXml(filename: $filename, headers: $headers, writerCallback: $callback),
            ExportFormat::TSV => $this->downloader->streamTsv(filename: $filename, writerCallback: $callback),
        };
    }
}
