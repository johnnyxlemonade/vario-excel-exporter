<?php

declare(strict_types=1);

namespace App\Infrastructure\Xml;

use App\Export\FinishingRowWriter;
use App\Export\RowWriter;
use RuntimeException;

final class XmlRowWriter implements RowWriter, FinishingRowWriter
{
    /** @var resource */
    private $handle;

    /** @var list<string> */
    private array $columns;

    /**
     * @param resource $handle
     * @param list<string> $columns
     */
    public function __construct($handle, array $columns)
    {
        if (!is_resource($handle)) {
            throw new RuntimeException('Invalid XML output handle');
        }

        $this->handle = $handle;
        $this->columns = $columns;

        fwrite($this->handle, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        fwrite($this->handle, "<rows>\n");
    }

    /**
     * @param list<string|int|float|bool|null> $row
     */
    public function write(array $row): void
    {
        fwrite($this->handle, "  <row>\n");

        foreach ($row as $index => $value) {

            $name = $this->columns[$index] ?? ('col' . $index);

            $escaped = htmlspecialchars(
                (string) ($value ?? ''),
                ENT_XML1 | ENT_COMPAT,
                'UTF-8'
            );

            fwrite(
                $this->handle,
                "    <{$name}>{$escaped}</{$name}>\n"
            );
        }

        fwrite($this->handle, "  </row>\n");
    }

    public function finish(): void
    {
        fwrite($this->handle, "</rows>\n");
    }
}
