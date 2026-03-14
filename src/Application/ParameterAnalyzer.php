<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Parameter\Parameter;

class ParameterAnalyzer
{
    /** @var array<string,true> */
    private array $ignoreMap;

    /** @var array<string,true> */
    private array $enabledMap;

    /**
     * @param list<string> $ignore
     * @param list<string> $enabled
     */
    public function __construct(array $ignore, array $enabled = [])
    {
        $this->ignoreMap  = array_fill_keys($ignore, true);
        $this->enabledMap = array_fill_keys($enabled, true);
    }

    /** @return list<string> */
    public function getIgnore(): array
    {
        return array_keys($this->ignoreMap);
    }

    /** @return list<string> */
    public function getEnabled(): array
    {
        return array_keys($this->enabledMap);
    }

    public function getConfigHash(): string
    {
        $ignore  = $this->getIgnore();
        $enabled = $this->getEnabled();

        sort($ignore);
        sort($enabled);

        return sha1(json_encode([
            'ignore' => $ignore,
            'enable' => $enabled,
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @param array<int,mixed> $headers
     * @param array<int,mixed> $labels
     * @param iterable<array<int,mixed>> $data
     *
     * @return list<Parameter>
     */
    public function analyze(array $headers, array $labels, iterable $data): array
    {
        $parameters = [];
        $valueMaps  = [];

        foreach ($headers as $i => $field) {

            $field = is_scalar($field) ? (string) $field : '';

            if (!str_starts_with($field, 'F:')) {
                continue;
            }

            $label = $labels[$i] ?? null;
            $name = is_scalar($label) ? trim((string) $label, '(){} ') : '';

            $ignored = isset($this->ignoreMap[$name]);
            $enabled = isset($this->enabledMap[$name]);

            if ($ignored && !$enabled) {
                continue;
            }

            $parameters[$i] = [
                'field' => $field,
                'name'  => $name,
                'index' => $i,
            ];

            $valueMaps[$i] = [];
        }

        foreach ($data as $row) {

            foreach ($parameters as $i => $_param) {

                $raw = $row[$i] ?? null;
                $value = is_scalar($raw) ? trim((string) $raw) : '';

                if ($value === '') {
                    continue;
                }

                $valueMaps[$i][$value] = true;
            }
        }

        $result = [];

        foreach ($parameters as $i => $meta) {

            $result[] = new Parameter(
                field: $meta['field'],
                index: $meta['index'],
                name: $meta['name'],
                values: array_keys($valueMaps[$i])
            );
        }

        return $result;
    }
}
