<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Validation;

use App\Infrastructure\DI\Definition\ServiceDefinition;

interface DefinitionValidatorInterface
{
    /**
     * @param list<ServiceDefinition> $definitions
     */
    public function validate(array $definitions): void;
}
