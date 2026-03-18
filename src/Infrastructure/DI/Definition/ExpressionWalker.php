<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Definition;

use App\Infrastructure\DI\Definition\Expression\ClassConstant;
use App\Infrastructure\DI\Definition\Expression\Expression;
use App\Infrastructure\DI\Definition\Expression\Parameter;
use App\Infrastructure\DI\Definition\Expression\ScalarValue;

final class ExpressionWalker
{
    public static function isLeaf(Expression $expr): bool
    {
        return
            $expr instanceof ScalarValue ||
            $expr instanceof ClassConstant ||
            $expr instanceof Parameter;
    }
}
