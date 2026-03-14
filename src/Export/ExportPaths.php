<?php

declare(strict_types=1);

namespace App\Export;

final class ExportPaths
{
    public function __construct(
        private readonly string $dir,
        private readonly string $hash
    ) {}

    public function getDir(): string
    {
        return $this->dir;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
