<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Validation;

use App\Infrastructure\DI\Definition\ServiceDefinition;

final class DuplicateServiceValidator implements DefinitionValidatorInterface
{
    /**
     * @param list<ServiceDefinition> $definitions
     */
    public function validate(array $definitions): void
    {
        $map = [];

        foreach ($definitions as $def) {
            if (isset($map[$def->id])) {
                throw new \LogicException(sprintf(
                    "[DI] Duplicate service definition for '%s'.\nFirst defined as '%s()', duplicate as '%s()'.",
                    $def->id,
                    $map[$def->id],
                    $def->methodName
                ));
            }

            $map[$def->id] = $def->methodName;
        }
    }
}
