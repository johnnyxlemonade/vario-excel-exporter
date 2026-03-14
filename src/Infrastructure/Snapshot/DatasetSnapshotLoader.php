<?php

declare(strict_types=1);

namespace App\Infrastructure\Snapshot;

use App\Domain\Dataset\ExcelDataset;
use App\Infrastructure\IO\NdjsonReader;
use Generator;
use JsonException;

final class DatasetSnapshotLoader
{
    public function __construct(
        private readonly NdjsonReader $reader
    ) {}

    /**
     * @throws JsonException
     */
    public function load(string $file): ExcelDataset
    {
        /** @var array<int,mixed> $headers */
        $headers = [];

        /** @var array<int,mixed> $labels */
        $labels = [];

        foreach ($this->reader->read($file) as $row) {

            $type = $row['type'] ?? null;

            if ($type === 'headers' && isset($row['data']) && is_array($row['data'])) {
                $headers = array_values($row['data']);
            }

            if ($type === 'labels' && isset($row['data']) && is_array($row['data'])) {
                $labels = array_values($row['data']);
                break;
            }
        }

        $rowsFactory = function () use ($file): Generator {

            foreach ($this->reader->read($file) as $row) {

                if (($row['type'] ?? null) !== 'row') {
                    continue;
                }

                $data = $row['data'] ?? null;

                if (!is_array($data)) {
                    continue;
                }

                /** @var array<int,mixed> $data */
                yield array_values($data);
            }
        };

        return new ExcelDataset(
            headers: $headers,
            labels: $labels,
            rowsFactory: $rowsFactory
        );
    }
}
