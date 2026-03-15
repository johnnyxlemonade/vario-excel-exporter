<?php

declare(strict_types=1);

namespace App\Infrastructure\Csv;

use App\Export\RowWriter;

final class CsvRowWriter implements RowWriter
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
     * @param list<string|int|float|bool|null> $row
     */
    public function write(array $row): void
    {
        fputcsv($this->handle, $row, ';');
    }
}
