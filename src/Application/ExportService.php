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
use App\Infrastructure\Http\DownloadMime;
use App\Infrastructure\Http\FileDownloader;
use App\Infrastructure\Time\Clock;
use Exception;
use JsonException;

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

    /**
     * @throws JsonException
     */
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
     * @throws Exception
     */
    public function streamFilters(
        ExportPaths $paths,
        ExportFormat $format,
        array $parameters,
    ): never {

        $headers = $this->filterExporter->config()->headers();

        $callback = fn(RowWriter $writer)
        => $this->filterExporter->export($parameters, $writer);

        $this->stream($paths, $format, 'filters', $headers, $callback);
    }

    /**
     * @param list<Parameter> $parameters
     * @param iterable<int, array<int, mixed>> $rows
     * @throws Exception
     */
    public function streamMapping(
        ExportPaths $paths,
        ExportFormat $format,
        iterable $rows,
        array $parameters,
    ): never {

        $headers = $this->productFilterMapper->config()->headers();

        $callback = fn(RowWriter $writer)
        => $this->productFilterMapper->export(
            $parameters,
            $rows,
            $writer
        );

        $this->stream($paths, $format, 'mapping', $headers, $callback);
    }

    /**
     * @param list<string> $headers
     * @param callable(RowWriter):void $callback
     * @throws Exception
     */
    private function stream(
        ExportPaths $paths,
        ExportFormat $format,
        string $prefix,
        array $headers,
        callable $callback
    ): never {

        $filename = sprintf(
            '%s_%s_%s.%s',
            $prefix,
            $paths->getHash(),
            $this->clock->exportTimestamp(),
            $format->value
        );

        $this->downloader->stream(
            mime: DownloadMime::fromFormat($format),
            filename: $filename,
            headers: $headers,
            callback: $callback
        );
    }
}
