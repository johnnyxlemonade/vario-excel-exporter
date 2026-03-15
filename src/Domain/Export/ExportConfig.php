<?php

declare(strict_types=1);

namespace App\Domain\Export;

/**
 * @phpstan-type HeaderList list<string>
 */
interface ExportConfig
{
    /**
     * @return HeaderList
     */
    public function headers(): array;
}
