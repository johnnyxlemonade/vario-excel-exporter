<?php

declare(strict_types=1);

namespace App\Domain\Dataset;

use Generator;

final class ExcelDataset
{
    /**
     * @var callable(): Generator<int, array<int,mixed>>
     */
    private $rowsFactory;

    /**
     * @param array<int,mixed> $headers
     * @param array<int,mixed> $labels
     * @param callable(): Generator<int, array<int,mixed>> $rowsFactory
     */
    public function __construct(
        private readonly array $headers,
        private readonly array $labels,
        callable $rowsFactory
    ) {
        $this->rowsFactory = $rowsFactory;
    }

    /**
     * @return array<int,mixed>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array<int,mixed>
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @return Generator<int, array<int,mixed>>
     */
    public function getRows(): Generator
    {
        $factory = $this->rowsFactory;

        return $factory();
    }

    /**
     * Lazy map
     * @param callable(array<int,mixed>): array<int,mixed> $mapper
     */
    public function mapRows(callable $mapper): self
    {
        $factory = $this->rowsFactory;

        return new self(
            $this->headers,
            $this->labels,
            function () use ($factory, $mapper): Generator {

                foreach ($factory() as $row) {
                    yield $mapper($row);
                }

            }
        );
    }

    /**
     * Lazy filter
     * @param callable(array<int,mixed>): bool $predicate
     */
    public function filterRows(callable $predicate): self
    {
        $factory = $this->rowsFactory;

        return new self(
            $this->headers,
            $this->labels,
            function () use ($factory, $predicate): Generator {

                foreach ($factory() as $row) {

                    if ($predicate($row)) {
                        yield $row;
                    }

                }

            }
        );
    }
}
