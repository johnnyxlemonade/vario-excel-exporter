<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Dataset\ExcelDataset;

final class DatasetCompareService
{
    private const PRODUCT_ID_COLUMN = 0;
    private const CODE_PUBLIC_COLUMN = 1;
    private const NAME_FULL_COLUMN = 2;

    public function compare(ExcelDataset $oldDataset, ExcelDataset $newDataset): DatasetCompareResult
    {
        $oldHeaders = $this->normalizeValues($oldDataset->getHeaders());
        $newHeaders = $this->normalizeValues($newDataset->getHeaders());

        $oldLabels = $this->normalizeValues($oldDataset->getLabels());
        $newLabels = $this->normalizeValues($newDataset->getLabels());

        $oldRowsByProductId = $this->indexRowsByProductId($oldDataset);
        $newRowsByProductId = $this->indexRowsByProductId($newDataset);

        return new DatasetCompareResult(
            oldRowCount: count($oldRowsByProductId),
            newRowCount: count($newRowsByProductId),
            addedHeaders: array_values(array_diff($newHeaders, $oldHeaders)),
            removedHeaders: array_values(array_diff($oldHeaders, $newHeaders)),
            addedLabels: array_values(array_diff($newLabels, $oldLabels)),
            removedLabels: array_values(array_diff($oldLabels, $newLabels)),
            missingRows: array_values(array_diff_key($oldRowsByProductId, $newRowsByProductId)),
            addedRows: array_values(array_diff_key($newRowsByProductId, $oldRowsByProductId)),
        );
    }

    /**
     * @return array<string, array{product_id: string, code_public: string, name_full: string}>
     */
    private function indexRowsByProductId(ExcelDataset $dataset): array
    {
        $indexedRows = [];

        foreach ($dataset->getRows() as $row) {
            $productId = $this->normalizeScalar($row[self::PRODUCT_ID_COLUMN] ?? null);

            if ($productId === '') {
                continue;
            }

            $indexedRows[$productId] = [
                'product_id' => $productId,
                'code_public' => $this->normalizeScalar($row[self::CODE_PUBLIC_COLUMN] ?? null),
                'name_full' => $this->normalizeScalar($row[self::NAME_FULL_COLUMN] ?? null),
            ];
        }

        return $indexedRows;
    }

    /**
     * @param array<int, mixed> $values
     * @return list<string>
     */
    private function normalizeValues(array $values): array
    {
        $normalized = [];

        foreach ($values as $value) {
            $value = $this->normalizeScalar($value);

            if ($value === '') {
                continue;
            }

            $normalized[] = $value;
        }

        return $normalized;
    }

    private function normalizeScalar(mixed $value): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        return trim((string) $value);
    }
}
