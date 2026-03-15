<?php

declare(strict_types=1);

namespace App\Export;

use App\Domain\Export\ExportConfig;
use App\Domain\Filter\ProductFilterExportConfig;
use App\Domain\Parameter\Parameter;

final class ProductFilterMapper implements ConfigurableExporter
{
    public function __construct(
        private readonly ProductFilterExportConfig $config
    ) {}

    public function config(): ExportConfig
    {
        return $this->config;
    }

    /**
     * @param list<Parameter> $parameters
     * @param iterable<int, array<int, mixed>> $data
     */
    public function export(
        array $parameters,
        iterable $data,
        RowWriter $writer
    ): void {

        $headers = $this->config->headers();

        if ($writer instanceof HeaderRowWriter) {
            $writer->writeHeader($headers);
        } else {
            $writer->write($headers);
        }

        foreach ($data as $row) {

            $rawProduct = $row[1] ?? null;

            if (!is_scalar($rawProduct)) {
                continue;
            }

            $productCode = trim((string) $rawProduct);

            if ($productCode === '') {
                continue;
            }

            foreach ($parameters as $parameter) {

                $index = $parameter->getIndex();
                $rawValue = $row[$index] ?? null;

                if (!is_scalar($rawValue)) {
                    continue;
                }

                $value = trim((string) $rawValue);

                if ($value === '') {
                    continue;
                }

                $writer->write([
                    $productCode,
                    $parameter->getName(),
                    $value,
                ]);
            }
        }
    }
}
