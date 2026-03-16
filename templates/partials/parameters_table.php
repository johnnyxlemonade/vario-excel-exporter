<?php

declare(strict_types=1);

/** @var \App\Presentation\View\TemplateRenderer $view */
/** @var list<\App\Domain\Parameter\Parameter> $parameters */

?>

<div class="my-3 p-3 bg-body rounded shadow-sm">

    <h6 class="border-bottom pb-2 mb-3">Detected parameters</h6>

    <div class="search-wrapper mb-3">
        <input
            id="paramSearch"
            type="text"
            class="form-control"
            placeholder="Search parameter, field or value..."
        >
    </div>

    <div class="table-responsive">

        <table id="paramTable" class="table table-sm table-hover align-middle">

            <thead class="table-dark">
            <tr>
                <th style="width:80px">Column</th>
                <th style="width:260px">Parameter</th>
                <th style="width:150px">Field</th>
                <th style="width:120px">Values</th>
                <th>Available values</th>
            </tr>
            </thead>

            <tbody class="table-group-divider">

            <?php
            $lastIndex = count($parameters) - 1;
            ?>

            <?php foreach ($parameters as $i => $parameter) : ?>

                <?php
                $values = $parameter->getValues();
                sort($values);

                $isLast = $i === $lastIndex;
                ?>

                <tr
                    data-row="filterable"
                    class="<?= $isLast ? 'is-last-filterable' : 'is-not-last-filterable' ?>"
                >

                    <td class="text-muted">
                        <?= $parameter->getIndex() ?>
                    </td>

                    <td class="param-name">
                        <?= $view->e($parameter->getName()) ?>
                    </td>

                    <td>
                        <code><?= $view->e($parameter->getField()) ?></code>
                    </td>

                    <td>
                        <span class="badge bg-primary"><?= count($values) ?></span>
                    </td>

                    <td>

                        <div class="value-list">

                            <?php foreach ($values as $value) : ?>

                                <span class="badge text-bg-secondary value-badge"><?= $view->e($value) ?></span>

                            <?php endforeach; ?>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

            <tr id="noResultsRow" data-row="empty" class="d-none">
                <td colspan="5" class="text-center text-muted">
                    <p class="alert alert-light">No parameters found for "<span id="searchTerm"></span>"</p>
                </td>
            </tr>

            </tbody>
        </table>

    </div>
</div>
