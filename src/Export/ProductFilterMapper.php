<?php

declare(strict_types=1);

namespace App\Export;

use App\Domain\Filter\ProductFilterExportConfig;
use App\Domain\Parameter\Parameter;

class ProductFilterMapper
{
    public function __construct(
        private readonly ProductFilterExportConfig $config
    ) {}

    /**
     * @param list<Parameter> $parameters
     * @param iterable<int, array<int, mixed>> $data
     * @param callable(list<string|int|float|bool|null>):void $writeRow
     */
    public function export(
        array $parameters,
        iterable $data,
        callable $writeRow
    ): void {

        // header
        $writeRow($this->config->headers());

        foreach ($data as $row) {

            $rawProduct = $row[1] ?? null;

            if (!is_scalar($rawProduct)) {
                continue;
            }

            $productCode = trim((string) $rawProduct);

            if ($productCode === '') {
                continue;
            }

            foreach ($parameters as $parameter) {

                $index = $parameter->getIndex();

                $rawValue = $row[$index] ?? null;

                if (!is_scalar($rawValue)) {
                    continue;
                }

                $value = trim((string) $rawValue);

                if ($value === '') {
                    continue;
                }

                $writeRow([
                    $productCode,
                    $parameter->getName(),
                    $value,
                ]);
            }
        }
    }
}
