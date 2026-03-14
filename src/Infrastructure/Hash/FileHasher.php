<?php

declare(strict_types=1);

namespace App\Infrastructure\Hash;

final class FileHasher
{
    public function sha1(string $file): string
    {
        $hash = sha1_file($file);

        if ($hash === false) {
            throw new \RuntimeException("Cannot calculate sha1 for file: {$file}");
        }

        return $hash;
    }

    public function sha1Short(string $file, int $length = 12): string
    {
        $hash = $this->sha1($file);

        return substr($hash, 0, $length);
    }

    public function combinedShort(string ...$parts): string
    {
        return substr(sha1(implode('', $parts)), 0, 12);
    }
}
