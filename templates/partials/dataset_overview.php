<?php

declare(strict_types=1);

/** @var \App\Presentation\View\TemplateRenderer $view */
/** @var list<\App\Domain\Parameter\Parameter> $parameters */
/** @var list<\App\Domain\Filter\Filter> $filters */
/** @var string $sourceFile */
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

use App\Infrastructure\Http\QueryHelper;

$fileName = basename($sourceFile);

$parameterCount = count($parameters);

$valueCount = 0;
foreach ($parameters as $p) {
    $valueCount += count($p->getValues());
}

?>

<div class="my-3 p-3 bg-body rounded shadow-sm">

    <h6 class="border-bottom pb-2 mb-3"><?= $view->e($view->t('dataset.overview')) ?></h6>

    <div class="row mb-3">

        <div class="col-md-3">
            <div class="card text-bg-light">
                <div class="card-body">
                    <strong><?= $view->e($fileName) ?></strong><br>
                    <small><?= $view->e($view->t('dataset.source')) ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-light">
                <div class="card-body">
                    <strong><code><?= $view->e($datasetHash) ?></code></strong><br>
                    <small><?= $view->e($view->t('dataset.hash')) ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-light">
                <div class="card-body">
                    <strong><?= $parameterCount ?></strong><br>
                    <small><?= $view->e($view->t('dataset.parameters_detected')) ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-light">
                <div class="card-body">
                    <strong><?= $valueCount ?></strong><br>
                    <small><?= $view->e($view->t('dataset.total_values')) ?></small>
                </div>
            </div>
        </div>

    </div>


    <?php if ($filters !== []) : ?>

        <div class="mb-3">

            <small class="text-muted d-block mb-2">
                <strong><?= $view->e($view->t('dataset.ignored_parameters')) ?>:</strong>
                <?= $view->e($view->t('dataset.ignored_parameters_help')) ?>
            </small>

            <div class="value-list">

                <?php

                $currentQuery = QueryHelper::all();
                unset($currentQuery['download']);

                foreach ($filters as $filter) :

                    $slug = $filter->getSlug();
                    $enabled = $filter->isEnabled();

                    $newQuery = $currentQuery;

                    if ($enabled) {

                        unset($newQuery[$slug]);
                        $class = 'text-bg-success';

                    } else {

                        $newQuery[$slug] = 1;
                        $class = 'text-bg-secondary';

                    }

                    $url = '?' . http_build_query($newQuery);

                    ?>

                    <a href="<?= $view->e($url) ?>" class="text-decoration-none">
                        <span class="badge <?= $class ?> value-badge"
                              data-bs-toggle="tooltip"
                              title="<?= $view->e($enabled ? $view->t('dataset.click_to_ignore') : $view->t('dataset.click_to_enable')) ?>"><?= $view->e($filter->getName()) ?></span>
                    </a>

                <?php endforeach; ?>

            </div>
        </div>

    <?php endif; ?>


    <h6 class="border-bottom pb-2 mb-3">
        <?= $view->e($view->t('dataset.exports')) ?>
    </h6>

    <p class="text-muted">
        <?= $view->e($view->t('dataset.exports_description')) ?>
    </p>

    <div class="d-grid gap-2 d-md-flex mt-4">

        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-filter"></i> <?= $view->e($view->t('dataset.download_filters')) ?>
            </button>

            <ul class="dropdown-menu shadow">

                <li>
                    <a class="dropdown-item" href="<?= $view->e($filtersXlsx) ?>">
                        <i class="fa fa-file-excel-o text-success" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.xlsx')) ?>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="<?= $view->e($filtersCsv) ?>">
                        <i class="fa fa-file-text-o text-info" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.csv')) ?>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="<?= $view->e($filtersJson) ?>">
                        <i class="fa fa-file-code-o text-warning" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.json')) ?>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="<?= $view->e($filtersXml) ?>">
                        <i class="fa fa-file-code-o text-secondary" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.xml')) ?>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="<?= $view->e($filtersTsv) ?>">
                        <i class="fa fa-file-text-o text-secondary" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.tsv')) ?>
                    </a>
                </li>

            </ul>
        </div>


        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-sitemap"></i> <?= $view->e($view->t('dataset.download_mapping')) ?>
            </button>

            <ul class="dropdown-menu shadow">

                <li>
                    <a class="dropdown-item" href="<?= $view->e($mappingXlsx) ?>">
                        <i class="fa fa-file-excel-o text-success" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.xlsx')) ?>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="<?= $view->e($mappingCsv) ?>">
                        <i class="fa fa-file-text-o text-info" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.csv')) ?>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="<?= $view->e($mappingJson) ?>">
                        <i class="fa fa-file-code-o text-warning" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.json')) ?>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="<?= $view->e($mappingXml) ?>">
                        <i class="fa fa-file-code-o text-secondary" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.xml')) ?>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="<?= $view->e($mappingTsv) ?>">
                        <i class="fa fa-file-text-o text-secondary" style="width: 20px;"></i>
                        <?= $view->e($view->t('format.tsv')) ?>
                    </a>
                </li>

            </ul>
        </div>

    </div>

</div>
