<?php

declare(strict_types=1);

namespace App\Dataset;

final class DatasetResolver
{
    public function __construct(
        private readonly DatasetCollection $datasets,
    ) {}

    public function resolve(?string $key): DatasetDefinition
    {
        if ($key !== null && $key !== '' && $this->datasets->has($key)) {
            return $this->datasets->get($key);
        }

        return $this->datasets->first();
    }
}
