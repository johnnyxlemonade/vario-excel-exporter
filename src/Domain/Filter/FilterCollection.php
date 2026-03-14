<?php

declare(strict_types=1);

namespace App\Domain\Filter;

final class FilterCollection
{
    /** @var list<Filter> */
    private array $filters;

    /**
     * @param list<Filter> $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return list<Filter>
     */
    public function all(): array
    {
        return $this->filters;
    }

    /**
     * @return list<string>
     */
    public function names(): array
    {
        $names = [];

        foreach ($this->filters as $filter) {
            $names[] = $filter->getName();
        }

        return $names;
    }

    /**
     * @return list<string>
     */
    public function enabled(): array
    {
        $enabled = [];

        foreach ($this->filters as $filter) {
            if ($filter->isEnabled()) {
                $enabled[] = $filter->getName();
            }
        }

        return $enabled;
    }

}
