<?php

declare(strict_types=1);

use App\Application\DatasetCompareService;
use App\Infrastructure\Excel\ExcelReader;

require __DIR__ . '/../vendor/autoload.php';

$projectDir = dirname(__DIR__);

$oldFile = $projectDir . '/puvodni_export_vlastnosti_produktu.xlsx';
$newFile = $projectDir . '/export_vlastnosti_produktu.xlsx';
$outputDir = $projectDir . '/var/compare';

if (!is_file($oldFile)) {
    fwrite(STDERR, sprintf("Původní export neexistuje: %s\n", $oldFile));
    exit(1);
}

if (!is_file($newFile)) {
    fwrite(STDERR, sprintf("Nový export neexistuje: %s\n", $newFile));
    exit(1);
}

if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
    fwrite(STDERR, sprintf("Nelze vytvořit výstupní složku: %s\n", $outputDir));
    exit(1);
}

$reader = new ExcelReader();
$service = new DatasetCompareService();

$result = $service->compare(
    oldDataset: $reader->read($oldFile),
    newDataset: $reader->read($newFile),
);

echo PHP_EOL;
echo 'Původní export: ' . $result->oldRowCount() . PHP_EOL;
echo 'Nový export:    ' . $result->newRowCount() . PHP_EOL;
echo 'Rozdíl:         ' . $result->rowCountDifference() . PHP_EOL;
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
echo 'V novém chybí produktů: ' . $result->missingRowCount() . PHP_EOL;
echo 'V novém přibylo produktů: ' . $result->addedRowCount() . PHP_EOL;

writeRowsCsv(
    file: $outputDir . '/missing-in-new.csv',
    rows: $result->missingRows(),
);

writeRowsCsv(
    file: $outputDir . '/added-in-new.csv',
    rows: $result->addedRows(),
);

echo PHP_EOL;
echo 'CSV výstupy:' . PHP_EOL;
echo '  ' . $outputDir . '/missing-in-new.csv' . PHP_EOL;
echo '  ' . $outputDir . '/added-in-new.csv' . PHP_EOL;

/**
 * @param list<array{product_id: string, code_public: string, name_full: string}> $rows
 */
function writeRowsCsv(string $file, array $rows): void
{
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
