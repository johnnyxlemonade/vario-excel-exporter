<?php

declare(strict_types=1);

namespace App\Export;

interface RowWriter
{
    /**
     * @param list<string|int|float|bool|null> $row
     */
    public function write(array $row): void;
}
