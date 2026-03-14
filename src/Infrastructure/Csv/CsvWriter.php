<?php

declare(strict_types=1);

namespace App\Infrastructure\Csv;

use RuntimeException;

final class CsvWriter
{
    /**
     * @param list<string> $headers
     * @return resource
     */
    public function open(string $file, array $headers)
    {
        $dir = dirname($file);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $handle = fopen($file, 'w');

        if ($handle === false) {
            throw new RuntimeException("Cannot open file: {$file}");
        }

        fputcsv($handle, $headers, ';');

        return $handle;
    }

    /**
     * @param resource $handle
     * @param list<string> $row
     */
    public function write($handle, array $row): void
    {
        fputcsv($handle, $row, ';');
    }

    /**
     * @param resource $handle
     */
    public function close($handle): void
    {
        fclose($handle);
    }
}
