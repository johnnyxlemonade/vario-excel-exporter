<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use App\Infrastructure\DI\Definition\DefinitionBuilder;
use App\Infrastructure\DI\Definition\ServiceDefinition;
use App\Infrastructure\DI\Validation\DefinitionValidator;

final class ContainerBuilder
{
    /**
     * @return list<ServiceDefinition>
     */
    public function build(): array
    {
        $definitions = (new DefinitionBuilder())->build();

        (new DefinitionValidator())->validate($definitions);

        return $definitions;
    }
}
