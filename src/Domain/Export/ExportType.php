<?php

declare(strict_types=1);

namespace App\Domain\Export;

enum ExportType: string
{
    case FILTERS = 'filters';
    case MAPPING = 'mapping';

    public static function fromString(?string $value): ?self
    {
        return self::tryFrom((string) $value);
    }

    public static function fromQuery(?string $value): ?self
    {
        return $value === null ? null : self::tryFrom($value);
    }
}
