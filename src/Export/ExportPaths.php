<?php

declare(strict_types=1);

namespace App\Export;

final class ExportPaths
{
    private readonly string $filters;
    private readonly string $mapping;
    private readonly string $hash;

    public function __construct(
        string $filters,
        string $mapping,
        string $hash
    ) {
        $this->filters = $filters;
        $this->mapping = $mapping;
        $this->hash = $hash;
    }

    public function getFilters(): string
    {
        return $this->filters;
    }

    public function getMapping(): string
    {
        return $this->mapping;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
