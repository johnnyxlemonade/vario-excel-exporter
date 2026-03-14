<?php

declare(strict_types=1);

namespace App\Domain\Filter;

final class FilterExportConfig
{
    public function __construct(
        private readonly string $filterColumn = 'filter',
        private readonly string $valueColumn = 'value'
    ) {}

    /**
     * @return list<string>
     */
    public function headers(): array
    {
        return [
            $this->filterColumn,
            $this->valueColumn,
        ];
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
