<?php

declare(strict_types=1);

namespace App\Infrastructure\Export;

use App\Export\FinishingRowWriter;
use JsonException;

final class JsonRowWriter implements FinishingRowWriter
{
    /** @var resource */
    private $handle;

    /** @var list<string> */
    private array $headers;

    private bool $first = true;

    /**
     * @param resource $handle
     * @param list<string> $headers
     */
    public function __construct($handle, array $headers)
    {
        $this->handle = $handle;
        $this->headers = $headers;

        fwrite($this->handle, '[');
    }

    /**
     * @param list<string|int|float|bool|null> $row
     * @throws JsonException
     */
    public function write(array $row): void
    {
        if (!$this->first) {
            fwrite($this->handle, ',');
        }

        $object = [];

        foreach ($row as $i => $value) {
            $key = $this->headers[$i] ?? ('col' . $i);
            $object[$key] = $value;
        }

        $json = json_encode(
            $object,
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        fwrite($this->handle, $json);

        $this->first = false;
    }

    public function finish(): void
    {
        fwrite($this->handle, ']');
    }
}
