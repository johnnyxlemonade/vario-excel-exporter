<?php

declare(strict_types=1);

/** @var \App\Presentation\View\TemplateRenderer $view */
/** @var \App\Dataset\DatasetDefinition $dataset */
/** @var list<\App\Dataset\DatasetDefinition> $datasets */

if (count($datasets) <= 1) {
    return;
}

$currentLang = $view->locale();
?>

<div class="card app-card mb-3">
    <div class="card-body">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <div class="app-section-title mb-1">
                    <?= $view->e($view->t('dataset.title')) ?>
                </div>

                <div class="fw-semibold">
                    <?= $view->e($view->t($dataset->labelKey())) ?>
                </div>

                <div class="text-muted small">
                    <?= $view->e($dataset->file()) ?>
                </div>
            </div>

            <div class="btn-group dataset-switcher" role="group" aria-label="Dataset switcher">
                <?php foreach ($datasets as $item): ?>
                    <?php $isActive = $item->key() === $dataset->key(); ?>

                    <a
                        href="?lang=<?= $view->e($currentLang) ?>&dataset=<?= $view->e($item->key()) ?>"
                        class="btn btn-sm <?= $isActive ? 'btn-primary active' : 'btn-outline-primary' ?>"
                        <?= $isActive ? 'aria-current="page"' : '' ?>
                    >
                        <?= $view->e($view->t($item->labelKey())) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
