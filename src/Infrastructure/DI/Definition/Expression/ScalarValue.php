<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Definition\Expression;

final class ScalarValue implements Expression
{
    public function __construct(
        public readonly string|int|float|bool|null $value,
    ) {}
}
