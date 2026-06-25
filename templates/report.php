<?php

declare(strict_types=1);

/** @var \App\Presentation\View\TemplateRenderer $view */
/** @var list<\App\Domain\Parameter\Parameter> $parameters */
/** @var list<\App\Domain\Filter\Filter> $filters */
/** @var \App\Dataset\DatasetDefinition $dataset */
/** @var list<\App\Dataset\DatasetDefinition> $datasets */
/** @var string $sourceFile */
/** @var string $fileName */
/** @var string $datasetHash */

/** export urls */
/** @var string $filtersCsv */
/** @var string $filtersJson */
/** @var string $filtersXlsx */
/** @var string $filtersXml */
/** @var string $filtersTsv */

/** @var string $mappingCsv */
/** @var string $mappingJson */
/** @var string $mappingXlsx */
/** @var string $mappingXml */
/** @var string $mappingTsv */
?>

<!DOCTYPE html>
<html lang="<?= $view->e($view->locale()) ?>" class="no-js msie app-webp">

<head itemscope itemtype="https://schema.org/WebSite">

    <meta charset="utf-8">
    <meta name="author" content="Lemonade Framework – moderní PHP 8.1 framework pro e-commerce, CMS a integrace">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow">
    <meta name="generator" content="Lemonade CMS [lemonadeframework.cz]">
    <meta name="rating" content="General">
    <meta name="web_author" content="lemonadeframework.cz">
    <meta http-equiv="content-language" content="<?= $view->e($view->locale()) ?>">

    <title><?= $view->e($view->t('app.title')) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lemonadeframework.cz/fonts/fontawesome/webfont.css">

    <style>
        body { background:#f5f6f8; }

        .value-list {
            display:flex;
            flex-wrap:wrap;
            gap:4px;
        }

        .value-badge {
            font-size:.75rem;
        }

        .param-name {
            font-weight:500;
        }

        #paramTable > tbody > tr.is-last-filterable > td,
        #paramTable > tbody > tr.is-last-filterable > th {
            border-bottom:0 !important;
        }

        #paramTable > tbody > tr#noResultsRow > td {
            border-bottom:0 !important;
            padding:0.5rem 0 !important;
        }

        #paramTable > tbody > tr#noResultsRow .alert {
            margin:0;
        }

        #searchTerm {
            font-weight: bold;
        }

        .app-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        }

        .app-section-title {
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #6c757d;
        }

        .app-button-row {
            display: flex;
            flex-wrap: wrap;
            gap: .375rem;
        }

        .app-button-row .btn {
            min-width: 74px;
            font-weight: 600;
        }

        .dataset-switcher .btn {
            min-width: 140px;
        }

        .dataset-switcher .btn.active {
            pointer-events: none;
        }

        .export-group {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .export-group-title {
            font-size: .82rem;
            font-weight: 700;
            color: #495057;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .meta-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: .75rem .875rem;
        }

        .meta-label {
            display: block;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: .2rem;
        }

        .meta-value {
            font-weight: 700;
            color: #212529;
        }

        @media (max-width: 991.98px) {
            .meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .meta-grid {
                grid-template-columns: 1fr;
            }

            .dataset-switcher .btn,
            .app-button-row .btn {
                flex: 1 1 auto;
            }
        }
    </style>

</head>

<body>

<main class="container-fluid">

    <?php
    $view->partial('partials/header', [
        'title' => $view->t('app.title'),
        'subtitle' => $view->t('app.subtitle')
    ]);

    $view->partial('partials/dataset_switcher', [
        'dataset' => $dataset,
        'datasets' => $datasets,
    ]);

    $view->partial('partials/dataset_overview', [
        'parameters' => $parameters,
        'filters' => $filters,
        'sourceFile' => $sourceFile,
        'datasetHash' => $datasetHash,

        'filtersCsv'  => $filtersCsv,
        'filtersJson' => $filtersJson,
        'filtersXlsx' => $filtersXlsx,
        'filtersXml'  => $filtersXml,
        'filtersTsv'  => $filtersTsv,

        'mappingCsv'  => $mappingCsv,
        'mappingJson' => $mappingJson,
        'mappingXlsx' => $mappingXlsx,
        'mappingXml'  => $mappingXml,
        'mappingTsv'  => $mappingTsv,
    ]);

    $view->partial('partials/parameters_table', [
        'parameters' => $parameters
    ]);

    $view->partial('partials/footer', [
        'fileName' => $fileName
    ]);

    $view->partial('partials/scripts');
    ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
