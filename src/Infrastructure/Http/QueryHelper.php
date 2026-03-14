<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

final class QueryHelper
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = $_GET[$key] ?? $default;

        return is_scalar($value) ? (string)$value : $default;
    }

    public static function has(string $key): bool
    {
        return isset($_GET[$key]) && is_scalar($_GET[$key]);
    }

    /**
     * @return array<string,string>
     */
    public static function all(): array
    {
        $result = [];

        foreach ($_GET as $k => $v) {

            if (!is_scalar($v)) {
                continue;
            }

            $result[(string)$k] = (string)$v;
        }

        return $result;
    }
}
