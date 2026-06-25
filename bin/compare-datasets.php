<?php

declare(strict_types=1);

use App\Application\DatasetCompareService;
use App\Infrastructure\Excel\ExcelReader;

require __DIR__ . '/../vendor/autoload.php';

$projectDir = dirname(__DIR__);

$originalFile = $projectDir . '/assets/datasets/export_vlastnosti_produktu.xlsx';
$currentFile = $projectDir . '/assets/datasets/export_vlastnosti_produktu_20260624.xlsx';
$outputDir = $projectDir . '/var/compare';

if (!is_file($originalFile)) {
    fwrite(STDERR, sprintf("Původní export neexistuje: %s\n", $originalFile));
    exit(1);
}

if (!is_file($currentFile)) {
    fwrite(STDERR, sprintf("Aktuální export neexistuje: %s\n", $currentFile));
    exit(1);
}

if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
    fwrite(STDERR, sprintf("Nelze vytvořit výstupní složku: %s\n", $outputDir));
    exit(1);
}

$reader = new ExcelReader();
$service = new DatasetCompareService();

$result = $service->compare(
    oldDataset: $reader->read($originalFile),
    newDataset: $reader->read($currentFile),
);

echo PHP_EOL;
echo 'Původní export: ' . $result->oldRowCount() . PHP_EOL;
echo 'Aktuální export: ' . $result->newRowCount() . PHP_EOL;
echo 'Rozdíl:          ' . $result->rowCountDifference() . PHP_EOL;
echo PHP_EOL;

echo 'Přidané sloupce: ' . count($result->addedHeaders()) . PHP_EOL;
foreach ($result->addedHeaders() as $header) {
    echo '  + ' . $header . PHP_EOL;
}

echo PHP_EOL;
echo 'Odebrané sloupce: ' . count($result->removedHeaders()) . PHP_EOL;
foreach ($result->removedHeaders() as $header) {
    echo '  - ' . $header . PHP_EOL;
}

echo PHP_EOL;
echo 'V aktuálním exportu chybí produktů: ' . $result->missingRowCount() . PHP_EOL;
echo 'V aktuálním exportu přibylo produktů: ' . $result->addedRowCount() . PHP_EOL;

$missingFile = $outputDir . '/missing-in-current.csv';
$addedFile = $outputDir . '/added-in-current.csv';

writeRowsCsv(
    file: $missingFile,
    rows: $result->missingRows(),
);

writeRowsCsv(
    file: $addedFile,
    rows: $result->addedRows(),
);

echo PHP_EOL;
echo 'CSV výstupy:' . PHP_EOL;
echo '  ' . $missingFile . PHP_EOL;
echo '  ' . $addedFile . PHP_EOL;

/**
 * @param list<array{product_id: string, code_public: string, name_full: string}> $rows
 */
function writeRowsCsv(string $file, array $rows): void
{
    $directory = dirname($file);

    if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
        throw new RuntimeException(sprintf('Nelze vytvořit složku: %s', $directory));
    }

    $handle = fopen($file, 'wb');

    if ($handle === false) {
        throw new RuntimeException(sprintf('Nelze zapsat soubor: %s', $file));
    }

    fputcsv($handle, ['product_id', 'code_public', 'name_full'], ';');

    foreach ($rows as $row) {
        fputcsv(
            $handle,
            [
                $row['product_id'],
                $row['code_public'],
                $row['name_full'],
            ],
            ';'
        );
    }

    fclose($handle);
}
