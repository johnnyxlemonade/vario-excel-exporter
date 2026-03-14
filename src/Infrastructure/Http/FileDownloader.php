<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

final class FileDownloader
{
    /**
     * @param callable(callable(list<string|int|float|bool|null>):void):void $writerCallback
     */
    public function streamCsv(string $filename, callable $writerCallback): never
    {
        $this->prepareDownload([
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

        $handle = $this->openOutput();

        $writeRow = function (array $row) use ($handle): void {

            /** @var array<int|string, bool|float|int|string|null> $row */
            fputcsv($handle, $row, ';');
        };

        $writerCallback($writeRow);

        fflush($handle);
        fclose($handle);

        exit;
    }

    /**
     * @param callable(callable(list<string|int|float|bool|null>):void):void $writerCallback
     */
    public function streamJson(string $filename, callable $writerCallback): never
    {
        $this->prepareDownload([
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

        $handle = $this->openOutput();

        fwrite($handle, '[');

        $isFirst = true;

        $writeRow = function (array $row) use ($handle, &$isFirst): void {

            if (!$isFirst) {
                fwrite($handle, ',');
            }

            $json = json_encode($row, JSON_UNESCAPED_UNICODE);

            if ($json === false) {
                $json = 'null';
            }

            fwrite($handle, $json);

            $isFirst = false;
        };

        $writerCallback($writeRow);

        fwrite($handle, ']');

        fclose($handle);

        exit;
    }

    /**
     * @param callable(callable(list<string|int|float|bool|null>):void):void $writerCallback
     */
    public function streamExcel(string $filename, callable $writerCallback): never
    {
        $this->prepareDownload([
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

        $writer = new Writer();
        $writer->openToBrowser($filename);

        $writeRow = function (array $row) use ($writer): void {

            $cells = [];

            foreach ($row as $value) {
                $cells[] = Cell::fromValue($this->safeScalar($value));
            }

            $writer->addRow(new Row($cells));
        };

        $writerCallback($writeRow);

        $writer->close();

        exit;
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
            throw new \RuntimeException('Cannot open output stream');
        }

        return $handle;
    }

    private function safeScalar(mixed $value): string|int|float|bool|null
    {
        if (!is_scalar($value) && $value !== null) {
            return null;
        }

        return $value;
    }
}
