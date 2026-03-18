<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Validation;

use App\Infrastructure\DI\Definition\Expression\ArrayValue;
use App\Infrastructure\DI\Definition\Expression\Expression;
use App\Infrastructure\DI\Definition\Expression\MethodCall;
use App\Infrastructure\DI\Definition\Expression\NewInstance;
use App\Infrastructure\DI\Definition\Expression\Reference;
use App\Infrastructure\DI\Definition\Expression\StaticCall;
use App\Infrastructure\DI\Definition\ExpressionWalker;
use App\Infrastructure\DI\Definition\ServiceDefinition;

final class ReferenceValidator implements DefinitionValidatorInterface
{
    /**
     * @param list<ServiceDefinition> $definitions
     */
    public function validate(array $definitions): void
    {
        $map = [];

        foreach ($definitions as $def) {
            $map[$def->id] = true;
        }

        foreach ($definitions as $def) {
            $this->walk(
                $def->factory,
                function (Reference $ref) use ($map, $def) {
                    if (!isset($map[$ref->id])) {
                        throw new \LogicException(sprintf(
                            "[DI] Service '%s' depends on unknown service '%s'.",
                            $def->id,
                            $ref->id
                        ));
                    }
                },
                $def->id
            );
        }
    }

    private function walk(Expression $expr, callable $onReference, string $serviceId): void
    {
        if ($expr instanceof Reference) {
            $onReference($expr);
            return;
        }

        if ($expr instanceof NewInstance ||
            $expr instanceof MethodCall ||
            $expr instanceof StaticCall
        ) {
            foreach ($expr->arguments as $arg) {
                $this->walk($arg->value, $onReference, $serviceId);
            }
            return;
        }

        if ($expr instanceof ArrayValue) {
            foreach ($expr->items as $item) {
                $this->walk($item, $onReference, $serviceId);
            }
            return;
        }

        if (ExpressionWalker::isLeaf($expr)) {
            return;
        }

        throw new \LogicException(sprintf(
            "[DI] Unsupported expression '%s' in service '%s'.",
            $expr::class,
            $serviceId
        ));
    }
}
