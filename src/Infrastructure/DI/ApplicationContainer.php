<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use App\Application\ParameterProcessor;

interface ApplicationContainer
{
    public function set(string $id, object $service): void;

    public function has(string $id): bool;

    public function getParameter(string $name): mixed;

    public function getParameterProcessor(): ParameterProcessor;
}
