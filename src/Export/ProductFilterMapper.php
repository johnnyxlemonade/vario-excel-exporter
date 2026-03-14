<?php

declare(strict_types=1);

namespace App\Export;

use App\Domain\Filter\ProductFilterExportConfig;
use App\Domain\Parameter\Parameter;

class ProductFilterMapper
{
    public function __construct(
        private readonly ProductFilterExportConfig $config
    ) {
    }

    /**
     * @param list<Parameter> $parameters
     * @param iterable<int, array<int, mixed>> $data
     * @param callable(array):void $writeRow
     */
    public function export(
        array $parameters,
        iterable $data,
        callable $writeRow
    ): void {

        // header
        $writeRow($this->config->headers());

        foreach ($data as $row) {

            $productCode = trim((string)($row[1] ?? ''));

            if ($productCode === '') {
                continue;
            }

            foreach ($parameters as $parameter) {

                $index = $parameter->getIndex();

                $value = trim((string)($row[$index] ?? ''));

                if ($value === '') {
                    continue;
                }

                $writeRow([
                    $productCode,
                    $parameter->getName(),
                    $value
                ]);
            }
        }
    }
}
