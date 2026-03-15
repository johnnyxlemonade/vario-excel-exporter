<?php

declare(strict_types=1);

namespace App\Export;

interface FinishingRowWriter extends RowWriter
{
    public function finish(): void;
}
