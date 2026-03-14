<?php

declare(strict_types=1);

namespace App\Infrastructure\Time;

use DateTimeImmutable;
use DateTimeZone;

final class Clock
{
    private const EXPORT_FORMAT = 'Ymd-His';

    public function __construct(
        private readonly DateTimeZone $timezone = new DateTimeZone('Europe/Prague')
    ) {}

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timezone);
    }

    public function exportTimestamp(): string
    {
        return $this->now()->format(self::EXPORT_FORMAT);
    }
}
