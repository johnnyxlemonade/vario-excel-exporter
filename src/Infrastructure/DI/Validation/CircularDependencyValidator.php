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

final class CircularDependencyValidator implements DefinitionValidatorInterface
{
    /**
     * @param list<ServiceDefinition> $definitions
     */
    public function validate(array $definitions): void
    {
        $graph = $this->buildGraph($definitions);

        $visited = [];
        $stack = [];

        foreach (array_keys($graph) as $serviceId) {
            $this->detectCycle($serviceId, $graph, $visited, $stack);
        }
    }

    /**
     * @param list<ServiceDefinition> $definitions
     * @return array<string, list<string>>
     */
    private function buildGraph(array $definitions): array
    {
        $graph = [];

        foreach ($definitions as $def) {
            $deps = [];
            $this->collectDependencies($def->factory, $deps);

            $graph[$def->id] = array_values(array_unique($deps));
        }

        return $graph;
    }

    /**
     * @param list<string> $deps
     */
    private function collectDependencies(Expression $expr, array &$deps): void
    {
        if ($expr instanceof Reference) {
            $deps[] = $expr->id;
            return;
        }

        if ($expr instanceof NewInstance ||
            $expr instanceof MethodCall ||
            $expr instanceof StaticCall
        ) {
            foreach ($expr->arguments as $arg) {
                $this->collectDependencies($arg->value, $deps);
            }
            return;
        }

        if ($expr instanceof ArrayValue) {
            foreach ($expr->items as $item) {
                $this->collectDependencies($item, $deps);
            }
            return;
        }

        if (ExpressionWalker::isLeaf($expr)) {
            return;
        }

        throw new \LogicException(sprintf(
            "[DI] Unsupported expression '%s' during circular dependency analysis.",
            $expr::class
        ));
    }

    /**
     * @param array<string, list<string>> $graph
     * @param array<string, bool> $visited
     * @param array<string, bool> $stack
     */
    private function detectCycle(
        string $serviceId,
        array $graph,
        array &$visited,
        array &$stack
    ): void {
        if (isset($stack[$serviceId])) {
            $cycle = array_keys($stack);
            $cycle[] = $serviceId;

            throw new \LogicException(sprintf(
                '[DI] Circular dependency detected: %s',
                implode(' → ', $cycle)
            ));
        }

        if (isset($visited[$serviceId])) {
            return;
        }

        $visited[$serviceId] = true;
        $stack[$serviceId] = true;

        foreach ($graph[$serviceId] ?? [] as $dep) {
            $this->detectCycle($dep, $graph, $visited, $stack);
        }

        unset($stack[$serviceId]);
    }
}
