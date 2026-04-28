<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use FilesystemIterator;
use HashContext;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class ContainerFactory
{
    private const CACHE_DIR = __DIR__ . '/../../../var/cache/di';
    private const GENERATED_NAMESPACE = 'App\\Infrastructure\\DI\\Generated';
    private const TEMPLATE_DIR = __DIR__ . '/../../../templates';
    private const LANG_DIR = __DIR__ . '/../../../lang';

    public function __construct(
        private readonly ContainerBuilder $builder = new ContainerBuilder(),
        private readonly ?ContainerDumper $dumper = null,
    ) {}

    /**
     * @param array<string, mixed> $parameters
     */
    public function create(array $parameters = []): ApplicationContainer
    {
        $className = $this->createClassName();
        $file = $this->createFilePath($className);
        $fqcn = self::GENERATED_NAMESPACE . '\\' . $className;

        if (!is_file($file)) {
            $this->ensureCacheDirectoryExists();

            $source = $this->getDumper()->dump(
                namespace: self::GENERATED_NAMESPACE,
                className: $className,
                definitions: $this->builder->build(),
            );

            $this->writeFile($file, $source);
        }

        require_once $file;

        /** @var Container $container */
        $container = new $fqcn($parameters);

        return $container;
    }

    public function clearCache(): void
    {
        if (!is_dir(self::CACHE_DIR)) {
            return;
        }

        $files = glob(self::CACHE_DIR . '/CompiledContainer_*.php');
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            @unlink($file);
        }
    }

    private function getDumper(): ContainerDumper
    {
        return $this->dumper ?? new ContainerDumper(
            templateDir: self::TEMPLATE_DIR,
            langDir: self::LANG_DIR,
        );
    }

    private function createClassName(): string
    {
        return 'CompiledContainer_' . $this->buildHash();
    }

    private function createFilePath(string $className): string
    {
        return self::CACHE_DIR . '/' . $className . '.php';
    }

    private function ensureCacheDirectoryExists(): void
    {
        if (is_dir(self::CACHE_DIR)) {
            return;
        }

        if (!mkdir(self::CACHE_DIR, 0777, true) && !is_dir(self::CACHE_DIR)) {
            throw new \RuntimeException('Cannot create DI cache directory: ' . self::CACHE_DIR);
        }
    }

    private function writeFile(string $file, string $source): void
    {
        if (file_put_contents($file, $source) === false) {
            throw new \RuntimeException('Cannot write compiled container: ' . $file);
        }
    }

    private function buildHash(): string
    {
        $files = [
            __FILE__,
            __DIR__ . '/ApplicationContainer.php',
            __DIR__ . '/Container.php',
            __DIR__ . '/ContainerBuilder.php',
            __DIR__ . '/ContainerDumper.php',

            // Definition layer
            __DIR__ . '/Definition/ServiceDefinition.php',
            __DIR__ . '/Definition/Argument.php',

            // Expressions
            __DIR__ . '/Definition/Expression/Expression.php',
            __DIR__ . '/Definition/Expression/Reference.php',
            __DIR__ . '/Definition/Expression/ScalarValue.php',
            __DIR__ . '/Definition/Expression/ClassConstant.php',
            __DIR__ . '/Definition/Expression/StaticCall.php',
            __DIR__ . '/Definition/Expression/NewInstance.php',
            __DIR__ . '/Definition/Expression/MethodCall.php',
            __DIR__ . '/Definition/Expression/ArrayValue.php',
            __DIR__ . '/Definition/Expression/Parameter.php',

            // Builder
            __DIR__ . '/Definition/DefinitionBuilder.php',

            // config
            __DIR__ . '/../../../composer.lock',
        ];

        $ctx = hash_init('sha256');

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            hash_update($ctx, $file);
            hash_update_file($ctx, $file);
        }

        $this->addDirectoryToHash($ctx, self::LANG_DIR);
        $this->addDirectoryToHash($ctx, self::TEMPLATE_DIR);

        return substr(hash_final($ctx), 0, 16);
    }


    private function addDirectoryToHash(HashContext $ctx, string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $dir,
                FilesystemIterator::SKIP_DOTS
            )
        );

        $files = [];

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        foreach ($files as $path) {
            hash_update($ctx, $path);
            hash_update_file($ctx, $path);
        }
    }
}
