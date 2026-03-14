<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Dataset\ExcelDataset;
use App\Domain\Parameter\Parameter;
use App\Domain\Parameter\ParameterSnapshotLoader;
use App\Domain\Parameter\ParameterSnapshotWriter;
use App\Infrastructure\Excel\ExcelReader;
use App\Infrastructure\Hash\FileHasher;
use App\Infrastructure\Snapshot\DatasetSnapshotLoader;
use App\Infrastructure\Snapshot\DatasetSnapshotWriter;

final class ParameterRepository
{
    public function __construct(
        private readonly ExcelReader $reader,
        private readonly ParameterAnalyzer $analyzer,
        private readonly DatasetSnapshotLoader $datasetLoader,
        private readonly DatasetSnapshotWriter $datasetWriter,
        private readonly ParameterSnapshotLoader $parameterLoader,
        private readonly ParameterSnapshotWriter $parameterWriter,
        private readonly FileHasher $hasher
    ) {}

    public function dataset(string $file, string $dir): ExcelDataset
    {
        $hash = $this->hasher->sha1Short($file);

        $snapshot = "{$dir}/dataset_{$hash}.json";

        if (is_file($snapshot)) {
            return $this->datasetLoader->load($snapshot);
        }

        $dataset = $this->reader->read($file);

        $this->datasetWriter->write($dataset, $snapshot);

        return $dataset;
    }

    /**
     * @return list<Parameter>
     * @throws \JsonException
     */
    public function parameters(
        ExcelDataset $dataset,
        string $file,
        string $dir
    ): array {

        $datasetHash = $this->hasher->sha1Short($file);
        $configHash = $this->analyzer->getConfigHash();

        $snapshot = "{$dir}/parameters_{$datasetHash}_{$configHash}.json";

        if (is_file($snapshot)) {
            return $this->parameterLoader->load($snapshot);
        }

        $parameters = $this->analyzer->analyze(
            $dataset->getHeaders(),
            $dataset->getLabels(),
            $dataset->getRows()
        );

        $this->parameterWriter->write($parameters, $snapshot);

        return $parameters;
    }
}
