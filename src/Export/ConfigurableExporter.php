<?php

declare(strict_types=1);

namespace App\Export;

use App\Domain\Export\ExportConfig;

interface ConfigurableExporter
{
    public function config(): ExportConfig;
}
