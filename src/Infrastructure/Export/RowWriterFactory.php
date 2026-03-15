<?php

declare(strict_types=1);

namespace App\Infrastructure\Export;

use App\Export\RowWriter;
use OpenSpout\Writer\XLSX\Writer;

final class RowWriterFactory
{
    /**
     * @param resource $handle
     */
    public function createCsv($handle): RowWriter
    {
        return new CsvRowWriter($handle);
    }

    /**
     * @param resource $handle
     */
    public function createTsv($handle): RowWriter
    {
        return new TsvRowWriter($handle);
    }

    /**
     * @param resource $handle
     * @param list<string> $headers
     */
    public function createJson($handle, array $headers): RowWriter
    {
        return new JsonRowWriter($handle, $headers);
    }

    public function createExcel(Writer $writer): RowWriter
    {
        return new ExcelRowWriter($writer);
    }

    /**
     * @param resource $handle
     * @param list<string> $headers
     */
    public function createXml($handle, array $headers): RowWriter
    {
        return new XmlRowWriter($handle, $headers);
    }
}
