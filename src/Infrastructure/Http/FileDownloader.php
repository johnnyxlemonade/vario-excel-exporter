<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Export\FinishingRowWriter;
use App\Export\RowWriter;
use App\Infrastructure\Export\RowWriterFactory;
use OpenSpout\Writer\XLSX\Writer;
use RuntimeException;

final class FileDownloader
{
    public function __construct(
        private readonly RowWriterFactory $factory
    ) {}

    /**
     * @param callable(RowWriter):void $writerCallback
     */
    public function streamCsv(string $filename, callable $writerCallback): never
    {
        $this->streamHandle(
            mime: DownloadMime::CSV,
            filename: $filename,
            writerFactory: fn($handle) => $this->factory->createCsv($handle),
            callback: $writerCallback
        );
    }

    /**
     * @param list<string> $headers
     * @param callable(RowWriter):void $writerCallback
     */
    public function streamJson(
        string $filename,
        array $headers,
        callable $writerCallback
    ): never {
        $this->streamHandle(
            mime: DownloadMime::JSON,
            filename: $filename,
            writerFactory: fn($handle) => $this->factory->createJson($handle, $headers),
            callback: $writerCallback
        );
    }

    /**
     * @param callable(RowWriter):void $writerCallback
     */
    public function streamExcel(string $filename, callable $writerCallback): never
    {
        $this->prepareDownload(
            $this->downloadHeaders(DownloadMime::XLSX, $filename)
        );

        $writer = new Writer();
        $writer->openToBrowser($filename);

        $rowWriter = $this->factory->createExcel($writer);

        $writerCallback($rowWriter);

        $writer->close();

        exit;
    }

    /**
     * @param list<string> $headers
     * @param callable(RowWriter):void $writerCallback
     */
    public function streamXml(
        string $filename,
        array $headers,
        callable $writerCallback
    ): never {
        $this->streamHandle(
            mime: DownloadMime::XML,
            filename: $filename,
            writerFactory: fn($handle) => $this->factory->createXml($handle, $headers),
            callback: $writerCallback
        );
    }

    /**
     * @param callable(RowWriter):void $writerCallback
     */
    public function streamTsv(string $filename, callable $writerCallback): never
    {
        $this->streamHandle(
            mime: DownloadMime::TSV,
            filename: $filename,
            writerFactory: fn($handle) => $this->factory->createTsv($handle),
            callback: $writerCallback
        );
    }

    /**
     * @param callable(resource):RowWriter $writerFactory
     * @param callable(RowWriter):void $callback
     */
    private function streamHandle(
        DownloadMime $mime,
        string $filename,
        callable $writerFactory,
        callable $callback
    ): never {

        $this->prepareDownload(
            $this->downloadHeaders($mime, $filename)
        );

        $handle = $this->openOutput();

        $writer = $writerFactory($handle);

        $callback($writer);

        if ($writer instanceof FinishingRowWriter) {
            $writer->finish();
        }

        fflush($handle);
        fclose($handle);

        exit;
    }

    /**
     * @return array<string,string>
     */
    private function downloadHeaders(DownloadMime $mime, string $filename): array
    {
        return [
            'Content-Type' => $mime->value,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
    }

    /**
     * @param array<string,string> $headers
     */
    private function prepareDownload(array $headers): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }

        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('X-Accel-Buffering: no');
    }

    /**
     * @return resource
     */
    private function openOutput()
    {
        $handle = fopen('php://output', 'w');

        if ($handle === false) {
            throw new RuntimeException('Cannot open output stream');
        }

        return $handle;
    }
}
