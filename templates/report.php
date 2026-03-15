<?php

declare(strict_types=1);

/** @var \App\Presentation\View\TemplateRenderer $view */
/** @var list<\App\Domain\Parameter\Parameter> $parameters */
/** @var list<\App\Domain\Filter\Filter> $filters */
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
<html lang="en" class="no-js msie app-webp">

<head itemscope itemtype="https://schema.org/WebSite">

    <meta charset="utf-8">
    <meta name="author" content="Lemonade Framework – moderní PHP 8.1 framework pro e-commerce, CMS a integrace">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow">
    <meta name="generator" content="Lemonade CMS [lemonadeframework.cz]">
    <meta name="rating" content="General">
    <meta name="web_author" content="lemonadeframework.cz">

    <title>Product Parameter Analyzer</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lemonadeframework.cz/fonts/fontawesome/webfont.css">

    <style>
        body{background:#f5f6f8;}
        .value-list{display:flex;flex-wrap:wrap;gap:4px;}
        .value-badge{font-size:.75rem;}
        .param-name{font-weight:500;}
        #paramTable tbody tr:last-child td,
        #paramTable tbody tr:last-child th{border-bottom:0;}
    </style>

</head>

<body>

<main class="container-fluid">

    <?php
    $view->partial('partials/header', [
        'title' => 'Product Parameter Analyzer',
        'subtitle' => 'KvalitníTeplo · Seyfor Vario'
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
