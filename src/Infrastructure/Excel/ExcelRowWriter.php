<?php

declare(strict_types=1);

namespace App\Infrastructure\Excel;

use App\Export\RowWriter;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use OpenSpout\Writer\XLSX\Writer;

final class ExcelRowWriter implements RowWriter
{
    public function __construct(
        private readonly Writer $writer
    ) {}

    /**
     * @param list<string|int|float|bool|null> $row
     * @throws IOException
     * @throws WriterNotOpenedException
     */
    public function write(array $row): void
    {
        $cells = [];

        foreach ($row as $value) {
            $cells[] = Cell::fromValue($this->safeScalar($value));
        }

        $this->writer->addRow(new Row($cells));
    }

    private function safeScalar(mixed $value): string|int|float|bool|null
    {
        if (!is_scalar($value) && $value !== null) {
            return null;
        }

        return $value;
    }
}
