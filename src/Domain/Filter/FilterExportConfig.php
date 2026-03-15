<?php

declare(strict_types=1);

namespace App\Domain\Filter;

use App\Domain\Export\ExportConfig;

final class FilterExportConfig implements ExportConfig
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
