<?php

declare(strict_types=1);

namespace App\Dataset;

use InvalidArgumentException;

final class DatasetCollection
{
    /**
     * @param list<DatasetDefinition> $items
     */
    public function __construct(
        private readonly array $items,
    ) {}

    public function has(string $key): bool
    {
        foreach ($this->items as $item) {
            if ($item->key() === $key) {
                return true;
            }
        }

        return false;
    }

    public function get(string $key): DatasetDefinition
    {
        foreach ($this->items as $item) {
            if ($item->key() === $key) {
                return $item;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Dataset "%s" is not defined.',
            $key
        ));
    }

    public function first(): DatasetDefinition
    {
        $first = $this->items[0] ?? null;

        if (!$first instanceof DatasetDefinition) {
            throw new InvalidArgumentException('Dataset collection is empty.');
        }

        return $first;
    }

    /**
     * @return list<DatasetDefinition>
     */
    public function all(): array
    {
        return $this->items;
    }
}
