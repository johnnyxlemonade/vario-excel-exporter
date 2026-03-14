<?php

declare(strict_types=1);

namespace App\Domain\Parameter;

use App\Infrastructure\IO\NdjsonReader;
use JsonException;

final class ParameterSnapshotLoader
{
    public function __construct(
        private readonly NdjsonReader $reader
    ) {
    }

    /**
     * @return list<Parameter>
     * @throws JsonException
     */
    public function load(string $file): array
    {
        $parameters = [];

        foreach ($this->reader->read($file) as $row) {

            $parameters[] = new Parameter(
                field:  $row['field'],
                index:  $row['index'],
                name:   $row['name'],
                values: $row['values']
            );
        }

        return $parameters;
    }
}
