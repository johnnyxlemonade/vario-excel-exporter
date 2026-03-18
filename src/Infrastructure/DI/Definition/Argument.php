<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Definition;

use App\Infrastructure\DI\Definition\Expression\Expression;

final class Argument
{
    public function __construct(
        public readonly Expression $value,
        public readonly ?string $name = null,
    ) {}
}
