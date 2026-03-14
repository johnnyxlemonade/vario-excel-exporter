<?php

declare(strict_types=1);

namespace App\Infrastructure\Excel;

use App\Domain\Dataset\ExcelDataset;
use OpenSpout\Reader\XLSX\Reader;

class ExcelReader
{
    public function read(string $file): ExcelDataset
    {
        $reader = new Reader();
        $reader->open($file);

        $sheet = $reader->getSheetIterator()->current();

        $headers = [];
        $labels  = [];

        $rowIndex = 0;

        foreach ($sheet->getRowIterator() as $row) {

            $data = $row->toArray();

            if ($rowIndex === 0) {
                $headers = $data;
            } elseif ($rowIndex === 1) {
                $labels = $data;
                break;
            }

            $rowIndex++;
        }

        $reader->close();

        $rowsFactory = function () use ($file) {

            $reader = new Reader();
            $reader->open($file);

            $sheet = $reader->getSheetIterator()->current();

            $rowIndex = 0;

            foreach ($sheet->getRowIterator() as $row) {

                if ($rowIndex < 2) {
                    $rowIndex++;
                    continue;
                }

                yield $row->toArray();

                $rowIndex++;
            }

            $reader->close();
        };

        return new ExcelDataset(
            headers: $headers,
            labels: $labels,
            rowsFactory: $rowsFactory
        );
    }
}
