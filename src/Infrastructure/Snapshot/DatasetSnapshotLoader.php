<?php

declare(strict_types=1);

namespace App\Infrastructure\Snapshot;

use App\Domain\Dataset\ExcelDataset;
use App\Infrastructure\IO\NdjsonReader;
use JsonException;

final class DatasetSnapshotLoader
{
    public function __construct(
        private readonly NdjsonReader $reader
    ) {
    }

    /**
     * @throws JsonException
     */
    public function load(string $file): ExcelDataset
    {
        $headers = [];
        $labels  = [];

        foreach ($this->reader->read($file) as $row) {

            $type = $row['type'] ?? null;

            if ($type === 'headers') {
                $headers = $row['data'];
            }

            if ($type === 'labels') {
                $labels = $row['data'];
                break;
            }
        }

        $rowsFactory = function () use ($file): \Generator {

            foreach ($this->reader->read($file) as $row) {

                if (($row['type'] ?? null) === 'row') {
                    yield $row['data'];
                }
            }

        };

        return new ExcelDataset(
            headers: $headers,
            labels:  $labels,
            rowsFactory: $rowsFactory
        );
    }
}
