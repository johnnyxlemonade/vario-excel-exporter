<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;

final class FileDownloader
{
    /**
     * @param callable(callable(list<mixed>):void):void $writerCallback
     */
    public function streamCsv(string $filename, callable $writerCallback): never
    {
        // vyčistit všechny buffery
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // nginx proxy buffering
        header('X-Accel-Buffering: no');

        $handle = fopen('php://output', 'w');

        if ($handle === false) {
            throw new \RuntimeException('Cannot open output stream');
        }

        $writeRow = function(array $row) use ($handle): void {
            fputcsv($handle, $row, ';');
        };

        $writerCallback($writeRow);

        fflush($handle);
        fclose($handle);

        exit;
    }

    /**
     * @param callable(callable(list<mixed>):void):void $writerCallback
     */
    public function streamJson(string $filename, callable $writerCallback): never
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('X-Accel-Buffering: no');

        $handle = fopen('php://output', 'w');

        // Začátek JSON pole
        fwrite($handle, '[');

        $isFirst = true;
        $writeRow = function(array $row) use ($handle, &$isFirst): void {
            // Před každý řádek kromě prvního dáme čárku
            if (!$isFirst) {
                fwrite($handle, ',');
            }

            // Zapíšeme jeden řádek jako JSON objekt/pole
            fwrite($handle, json_encode($row, JSON_UNESCAPED_UNICODE));

            $isFirst = false;
        };

        // Tady se spustí mapper a bude nám krmit $writeRow
        $writerCallback($writeRow);

        // Konec JSON pole
        fwrite($handle, ']');

        fclose($handle);
        exit;
    }

    /**
     * @param callable(callable(list<mixed>):void):void $writerCallback
     */
    public function streamExcel(string $filename, callable $writerCallback): never
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('X-Accel-Buffering: no');

        $writer = new Writer();
        $writer->openToBrowser($filename);

        $writeRow = function(array $row) use ($writer): void {

            $cells = [];

            foreach ($row as $value) {
                $cells[] = Cell::fromValue($value);
            }

            $writer->addRow(new Row($cells));
        };

        $writerCallback($writeRow);

        $writer->close();

        exit;
    }
}
