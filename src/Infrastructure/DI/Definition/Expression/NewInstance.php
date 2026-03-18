<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Definition\Expression;

use App\Infrastructure\DI\Definition\Argument;

final class NewInstance implements Expression
{
    /**
     * @param list<Argument> $arguments
     */
    public function __construct(
        public readonly string $className,
        public readonly array $arguments = [],
    ) {}
}
