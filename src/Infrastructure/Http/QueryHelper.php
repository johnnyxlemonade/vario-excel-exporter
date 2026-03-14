<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

final class QueryHelper
{
    /**
     * @template T of string|null
     * @param T $default
     * @return ($default is null ? string|null : string)
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = $_GET[$key] ?? $default;

        return self::scalarToString($value) ?? $default;
    }

    public static function has(string $key): bool
    {
        return self::scalarToString($_GET[$key] ?? null) !== null;
    }

    /**
     * @return array<string,string>
     */
    public static function all(): array
    {
        $result = [];

        foreach ($_GET as $k => $v) {
            $value = self::scalarToString($v);

            if ($value === null) {
                continue;
            }

            $result[(string) $k] = $value;
        }

        return $result;
    }

    private static function scalarToString(mixed $value): ?string
    {
        return is_scalar($value) ? (string) $value : null;
    }
}
