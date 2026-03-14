<?php

declare(strict_types=1);

namespace App\Domain\Filter;

use App\Infrastructure\Http\QueryHelper;

final class Filter
{
    public function __construct(
        private readonly string $name,
        private readonly string $slug
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isEnabled(): bool
    {
        return QueryHelper::has($this->slug);
    }
}
