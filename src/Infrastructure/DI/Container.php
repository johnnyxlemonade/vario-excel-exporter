<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

abstract class Container
{
    /** @var array<string, object> */
    protected array $instances = [];

    /** @param array<string, mixed> $parameters */
    public function __construct(
        protected array $parameters = []
    ) {}

    public function set(string $id, object $service): void
    {
        $this->instances[$id] = $service;
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    public function getParameter(string $name): mixed
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \RuntimeException("[DI] Missing parameter '{$name}'.");
        }

        return $this->parameters[$name];
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @param callable():T $factory
     * @return T
     */
    protected function share(string $id, callable $factory): object
    {
        if (isset($this->instances[$id])) {
            /** @var T */
            return $this->instances[$id];
        }

        $service = $factory();
        $this->instances[$id] = $service;

        /** @var T */
        return $service;
    }
}
