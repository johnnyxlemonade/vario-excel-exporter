<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Filter\FilterCollection;

final class ParameterAnalyzerFactory
{
    public function create(FilterCollection $filters): ParameterAnalyzer
    {
        return new ParameterAnalyzer(
            $filters->names(),
            $filters->enabled()
        );
    }
}
