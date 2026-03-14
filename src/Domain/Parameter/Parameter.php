<?php

declare(strict_types=1);

namespace App\Domain\Parameter;

final class Parameter
{
    /**
     * @param list<string> $values
     */
    public function __construct(
        private readonly string $field,
        private readonly int $index,
        private readonly string $name,
        private readonly array $values
    ) {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return list<string>
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
