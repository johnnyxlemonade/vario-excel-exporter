<?php

declare(strict_types=1);

namespace App\Export;

interface HeaderRowWriter
{
    /**
     * @param list<string> $headers
     */
    public function writeHeader(array $headers): void;
}
