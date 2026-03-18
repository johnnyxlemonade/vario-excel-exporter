<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Definition\Expression;

final class ArrayValue implements Expression
{
    /**
     * @param list<Expression> $items
     */
    public function __construct(
        public readonly array $items = [],
    ) {}
}
