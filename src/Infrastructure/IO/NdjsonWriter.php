<?php

declare(strict_types=1);

namespace App\Infrastructure\IO;

use RuntimeException;

final class NdjsonWriter
{
    /**
     * @param iterable<array<string,mixed>> $rows
     */
    public function write(iterable $rows, string $file): void
    {
        $handle = fopen($file, 'w');

        if ($handle === false) {
            throw new RuntimeException("Cannot write NDJSON file: {$file}");
        }

        flock($handle, LOCK_EX);

        foreach ($rows as $row) {

            fwrite(
                $handle,
                json_encode($row, JSON_UNESCAPED_UNICODE) . "\n"
            );
        }

        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
