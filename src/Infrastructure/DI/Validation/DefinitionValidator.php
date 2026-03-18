<?php

declare(strict_types=1);

namespace App\Infrastructure\DI\Validation;

final class DefinitionValidator implements DefinitionValidatorInterface
{
    public function validate(array $definitions): void
    {
        foreach ($this->validators() as $validator) {
            $validator->validate($definitions);
        }
    }

    /**
     * @return list<DefinitionValidatorInterface>
     */
    private function validators(): array
    {
        return [
            new DuplicateServiceValidator(),
            new ReferenceValidator(),
            new CircularDependencyValidator(),
        ];
    }
}
