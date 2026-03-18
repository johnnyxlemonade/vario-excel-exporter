<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Definition;

use App\Infrastructure\DI\Definition\Expression\Expression;

final class ServiceDefinition
{
    public function __construct(
        public readonly string $id,
        public readonly string $returnType,
        public readonly string $methodName,
        public readonly Expression $factory,
    ) {}
}
