<?php

declare(strict_types=1);

/** @var \App\Presentation\View\TemplateRenderer $view */
/** @var string $fileName */

?>

<footer class="py-5 mt-5 bg-light border-top">
    <div class="container-fluid">

        <div class="d-flex align-items-center">

            <div
                class="me-3 d-flex align-items-center justify-content-center"
                style="width:42px;height:42px;background:#212529;color:white;border-radius:8px;font-weight:600;font-size:20px;"
            >
                L
            </div>

            <div>
                <div class="fw-semibold">Lemonade Data Tools</div>

                <small class="text-muted">
                    Source dataset:
                    <code><?= $view->e($fileName) ?></code>
                </small>
            </div>

        </div>

    </div>
</footer>
