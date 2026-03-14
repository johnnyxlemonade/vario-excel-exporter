<?php

declare(strict_types=1);

namespace App\Domain\Filter;

/**
 * @phpstan-type ColumnMap array{
 *     product_code: string,
 *     filter: string,
 *     value: string
 * }
 */
final class ProductFilterExportConfig
{
    public function __construct(
        private readonly string $productCodeColumn = 'product_code',
        private readonly string $filterColumn = 'filter',
        private readonly string $valueColumn = 'value'
    ) {
    }

    /**
     * @return list<string>
     */
    public function headers(): array
    {
        return [
            $this->productCodeColumn,
            $this->filterColumn,
            $this->valueColumn
        ];
    }

    public function productCodeColumn(): string
    {
        return $this->productCodeColumn;
    }

    public function filterColumn(): string
    {
        return $this->filterColumn;
    }

    public function valueColumn(): string
    {
        return $this->valueColumn;
    }
}
