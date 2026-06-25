<?php

declare(strict_types=1);

namespace App\Application;

final class DatasetCompareResult
{
    /**
     * @param list<string> $addedHeaders
     * @param list<string> $removedHeaders
     * @param list<string> $addedLabels
     * @param list<string> $removedLabels
     * @param list<array{product_id: string, code_public: string, name_full: string}> $missingRows
     * @param list<array{product_id: string, code_public: string, name_full: string}> $addedRows
     */
    public function __construct(
        private readonly int $oldRowCount,
        private readonly int $newRowCount,
        private readonly array $addedHeaders,
        private readonly array $removedHeaders,
        private readonly array $addedLabels,
        private readonly array $removedLabels,
        private readonly array $missingRows,
        private readonly array $addedRows,
    ) {}

    public function oldRowCount(): int
    {
        return $this->oldRowCount;
    }

    public function newRowCount(): int
    {
        return $this->newRowCount;
    }

    public function rowCountDifference(): int
    {
        return $this->newRowCount - $this->oldRowCount;
    }

    /**
     * @return list<string>
     */
    public function addedHeaders(): array
    {
        return $this->addedHeaders;
    }

    /**
     * @return list<string>
     */
    public function removedHeaders(): array
    {
        return $this->removedHeaders;
    }

    /**
     * @return list<string>
     */
    public function addedLabels(): array
    {
        return $this->addedLabels;
    }

    /**
     * @return list<string>
     */
    public function removedLabels(): array
    {
        return $this->removedLabels;
    }

    /**
     * @return list<array{product_id: string, code_public: string, name_full: string}>
     */
    public function missingRows(): array
    {
        return $this->missingRows;
    }

    /**
     * @return list<array{product_id: string, code_public: string, name_full: string}>
     */
    public function addedRows(): array
    {
        return $this->addedRows;
    }

    public function missingRowCount(): int
    {
        return count($this->missingRows);
    }

    public function addedRowCount(): int
    {
        return count($this->addedRows);
    }
}
