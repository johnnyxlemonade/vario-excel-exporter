<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use App\Application\ParameterProcessor;
use App\Dataset\DatasetCollection;
use App\Dataset\DatasetDefinition;
use App\Dataset\DatasetResolver;

interface ApplicationContainer
{
    public function set(string $id, object $service): void;

    public function has(string $id): bool;

    public function getParameter(string $name): mixed;

    public function getDatasetCollection(): DatasetCollection;

    public function getDatasetResolver(): DatasetResolver;

    public function getDatasetDefinition(): DatasetDefinition;

    public function getParameterProcessor(): ParameterProcessor;
}
