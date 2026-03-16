<?php

declare(strict_types=1);

/** @var \App\Presentation\View\TemplateRenderer $view */
/** @var string $title */
/** @var string|null $subtitle */

use App\Infrastructure\Http\QueryHelper;

$subtitle ??= null;

$query = QueryHelper::all();

$csQuery = $query;
$csQuery['lang'] = 'cs';

$enQuery = $query;
$enQuery['lang'] = 'en';

?>

<div class="d-flex align-items-center p-3 my-3 text-white bg-dark rounded shadow-sm">

    <div class="lh-1">

        <h1 class="h5 mb-0 text-white">
            <?= $view->e($title) ?>
        </h1>

        <?php if ($subtitle) : ?>

            <small>
                <?= $view->e($subtitle) ?>
            </small>

        <?php endif; ?>

    </div>

    <div class="ms-auto">

        <div class="btn-group btn-group-sm" role="group">

            <a
                class="btn btn-outline-light <?= $view->locale() === 'en' ? 'active' : '' ?>"
                href="?<?= $view->e(http_build_query($enQuery)) ?>"
            >
                EN
            </a>

            <a
                class="btn btn-outline-light <?= $view->locale() === 'cs' ? 'active' : '' ?>"
                href="?<?= $view->e(http_build_query($csQuery)) ?>"
            >
                CS
            </a>

        </div>

    </div>

</div>
