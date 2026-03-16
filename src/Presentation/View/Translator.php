<?php

declare(strict_types=1);

namespace App\Presentation\View;

use RuntimeException;

final class Translator
{
    /** @var array<string,string> */
    private array $messages;

    /**
     * @param array<string,string> $messages
     */
    public function __construct(
        private readonly string $locale,
        array $messages
    ) {
        $this->messages = $messages;
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function t(string $key, mixed ...$params): string
    {
        $message = $this->messages[$key] ?? $key;

        if ($params === []) {
            return $message;
        }

        $stringParams = array_map(
            static fn(mixed $value): string => self::stringify($value),
            $params
        );

        return vsprintf($message, $stringParams);
    }

    private static function stringify(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value === null) {
            return '';
        }

        throw new RuntimeException('Unsupported translation parameter type.');
    }
}
