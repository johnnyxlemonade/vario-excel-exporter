<?php

declare(strict_types=1);

namespace App\Infrastructure\Snapshot;


use App\Domain\Dataset\ExcelDataset;
use App\Infrastructure\IO\NdjsonWriter;
use App\Infrastructure\Time\Clock;

final class DatasetSnapshotWriter
{
    public function __construct(
        private readonly NdjsonWriter $writer,
        private readonly Clock $clock
    ) {
    }

    public function write(ExcelDataset $dataset, string $file): void
    {
        $rows = (function () use ($dataset) {

            yield [
                'type' => 'meta',
                'data' => [
                    'created_at' => $this->clock->now()->format(DATE_ATOM),
                ],
            ];

            yield [
                'type' => 'headers',
                'data' => $dataset->getHeaders(),
            ];

            yield [
                'type' => 'labels',
                'data' => $dataset->getLabels(),
            ];

            foreach ($dataset->getRows() as $row) {

                $cleanRow = [];

                foreach ($row as $value) {

                    if ($value === null) {
                        $cleanRow[] = null;
                        continue;
                    }

                    $value = trim((string) $value);
                    $cleanRow[] = $value === '' ? null : $value;
                }

                yield [
                    'type' => 'row',
                    'data' => $cleanRow,
                ];
            }

        })();

        $this->writer->write($rows, $file);
    }
}
