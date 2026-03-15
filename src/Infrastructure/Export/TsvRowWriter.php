<?php

declare(strict_types=1);

namespace App\Infrastructure\Export;

use App\Export\RowWriter;

final class TsvRowWriter implements RowWriter
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
        $values = [];

        foreach ($row as $value) {
            $string = (string) ($value ?? '');

            $values[] = str_replace(
                ["\t", "\n", "\r"],
                [' ', ' ', ' '],
                $string
            );
        }

        fwrite($this->handle, implode("\t", $values) . "\n");
    }
}
