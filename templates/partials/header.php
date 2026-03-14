<?php

declare(strict_types=1);


/** @var \App\Presentation\View\TemplateRenderer $view */
/** @var string $title */
/** @var string|null $subtitle */

$subtitle ??= null;

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

</div>
