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
     * @param list<string> $headers
     * @param callable(RowWriter):void $callback
     * @throws \Exception
     */
    public function stream(
        DownloadMime $mime,
        string $filename,
        array $headers,
        callable $callback
    ): never {

        if ($mime === DownloadMime::XLSX) {
            $this->streamExcel($filename, $callback);
        }

        $this->prepareDownload(
            $this->downloadHeaders($mime, $filename)
        );

        $handle = $this->openOutput();

        $writer = $this->createWriter($handle, $mime, $headers);

        $callback($writer);

        if ($writer instanceof FinishingRowWriter) {
            $writer->finish();
        }

        fflush($handle);
        fclose($handle);

        exit;
    }

    /**
     * @param resource $handle
     * @param list<string> $headers
     */
    private function createWriter($handle, DownloadMime $mime, array $headers): RowWriter
    {
        if ($mime === DownloadMime::XLSX) {
            throw new RuntimeException('XLSX handled separately');
        }

        /** @var array<string, callable(): RowWriter> $map */
        $map = [
            DownloadMime::CSV->value  => fn() => $this->factory->createCsv($handle),
            DownloadMime::TSV->value  => fn() => $this->factory->createTsv($handle),
            DownloadMime::JSON->value => fn() => $this->factory->createJson($handle, $headers),
            DownloadMime::XML->value  => fn() => $this->factory->createXml($handle, $headers),
        ];

        return $map[$mime->value]();
    }

    /**
     * @param callable(RowWriter):void $callback
     */
    private function streamExcel(
        string $filename,
        callable $callback
    ): never {

        $this->prepareDownload(
            $this->downloadHeaders(DownloadMime::XLSX, $filename)
        );

        $writer = new Writer();
        $writer->openToBrowser($filename);

        $rowWriter = $this->factory->createExcel($writer);

        $callback($rowWriter);

        $writer->close();

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
