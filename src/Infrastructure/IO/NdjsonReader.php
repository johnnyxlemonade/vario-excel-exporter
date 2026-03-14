<?php

declare(strict_types=1);

namespace App\Infrastructure\IO;

use Generator;
use JsonException;
use RuntimeException;

final class NdjsonReader
{
    /**
     * @return Generator<int, array<string,mixed>>
     * @throws JsonException
     */
    public function read(string $file): Generator
    {
        $handle = fopen($file, 'r');

        if ($handle === false) {
            throw new RuntimeException("Cannot read NDJSON file: {$file}");
        }

        while (($line = fgets($handle)) !== false) {

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $data = json_decode(
                $line,
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if (!is_array($data)) {
                throw new RuntimeException("Invalid NDJSON row");
            }

            /** @var array<string,mixed> $data */
            yield $data;
        }

        fclose($handle);
    }
}
