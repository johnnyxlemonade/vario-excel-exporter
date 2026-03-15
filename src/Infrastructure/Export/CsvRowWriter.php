<?php

declare(strict_types=1);

namespace App\Infrastructure\Export;

use App\Export\HeaderRowWriter;
use App\Export\RowWriter;

final class CsvRowWriter implements RowWriter, HeaderRowWriter
{
    /** @var resource */
    private $handle;

    /**
     * @param resource $handle
     */
    public function __construct($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @param list<string> $headers
     */
    public function writeHeader(array $headers): void
    {
        $this->write($headers);
    }

    /**
     * @param list<string|int|float|bool|null> $row
     */
    public function write(array $row): void
    {
        fputcsv($this->handle, $row, ';');
    }
}
