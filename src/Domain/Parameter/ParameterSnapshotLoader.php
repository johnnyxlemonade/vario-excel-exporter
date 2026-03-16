<?php

declare(strict_types=1);

namespace App\Domain\Parameter;

use App\Infrastructure\IO\NdjsonReader;
use JsonException;

final class ParameterSnapshotLoader
{
    public function __construct(
        private readonly NdjsonReader $reader
    ) {}

    /**
     * @return list<Parameter>
     * @throws JsonException
     */
    public function load(string $file): array
    {
        $parameters = [];

        foreach ($this->reader->read($file) as $row) {

            $field = $row['field'] ?? null;
            $index = $row['index'] ?? null;
            $name  = $row['name'] ?? null;
            $values = $row['values'] ?? null;

            if (
                !is_string($field) ||
                !is_int($index) ||
                !is_string($name) ||
                !is_array($values)
            ) {
                continue;
            }

            /** @var list<string> $values */
            $values = array_values(
                array_map(
                    static fn($v) => (string) $v,
                    array_filter($values, static fn($v) => is_scalar($v))
                )
            );

            $parameters[] = new Parameter(
                field: $field,
                index: $index,
                name: $name,
                values: $values
            );
        }

        return $parameters;
    }
}
