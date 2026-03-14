<?php

declare(strict_types=1);

namespace App\Export;

use App\Domain\Filter\FilterExportConfig;
use App\Domain\Parameter\Parameter;

class FilterExporter
{
    public function __construct(
        private readonly FilterExportConfig $config
    ) {}

    /**
     * @param list<Parameter> $parameters
     * @param callable(list<string|int|float|bool|null>):void $writeRow
     */
    public function export(
        array $parameters,
        callable $writeRow
    ): void {

        // header
        $writeRow($this->config->headers());

        foreach ($parameters as $parameter) {

            $name = $parameter->getName();

            foreach ($parameter->getValues() as $value) {

                $writeRow([
                    $name,
                    $value,
                ]);
            }
        }
    }
}
