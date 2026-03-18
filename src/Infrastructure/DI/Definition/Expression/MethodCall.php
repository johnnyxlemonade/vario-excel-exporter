<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Definition\Expression;

use App\Infrastructure\DI\Definition\Argument;

final class MethodCall implements Expression
{
    /**
     * @param list<Argument> $arguments
     */
    public function __construct(
        public readonly Expression $target,
        public readonly string $methodName,
        public readonly array $arguments = [],
    ) {}
}
