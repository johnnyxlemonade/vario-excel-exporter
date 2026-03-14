<?php

declare(strict_types=1);

namespace App\Domain\Parameter;

use App\Infrastructure\IO\NdjsonWriter;

final class ParameterSnapshotWriter
{
    public function __construct(
        private readonly NdjsonWriter $writer
    ) {}

    /**
     * @param list<Parameter> $parameters
     */
    public function write(array $parameters, string $file): void
    {
        $rows = (function () use ($parameters) {

            foreach ($parameters as $p) {
                yield [
                    'field'  => $p->getField(),
                    'index'  => $p->getIndex(),
                    'name'   => $p->getName(),
                    'values' => $p->getValues(),
                ];
            }

        })();

        $this->writer->write($rows, $file);
    }
}
