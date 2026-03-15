<?php

declare(strict_types=1);

namespace App\Export;

use App\Domain\Export\ExportConfig;
use App\Domain\Filter\FilterExportConfig;
use App\Domain\Parameter\Parameter;

class FilterExporter implements ConfigurableExporter
{
    public function __construct(
        private readonly FilterExportConfig $config
    ) {}

    public function config(): ExportConfig
    {
        return $this->config;
    }

    /**
     * @param list<Parameter> $parameters
     */
    public function export(
        array $parameters,
        RowWriter $writer
    ): void {

        $headers = $this->config->headers();

        if ($writer instanceof HeaderRowWriter) {
            $writer->writeHeader($headers);
        } else {
            $writer->write($headers);
        }

        foreach ($parameters as $parameter) {

            $name = $parameter->getName();

            foreach ($parameter->getValues() as $value) {

                $writer->write([
                    $name,
                    $value,
                ]);
            }
        }
    }
}
