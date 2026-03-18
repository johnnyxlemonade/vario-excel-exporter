<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Definition\Expression;

final class ClosureFactory implements Expression
{
    /**
     * Raw body closure factory.
     *
     * The code should return the created service instance.
     */
    public function __construct(
        public readonly string $body,
        public readonly ?string $returnType = null,
    ) {}
}
