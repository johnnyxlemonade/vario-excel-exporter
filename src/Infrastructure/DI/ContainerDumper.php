<?php

declare(strict_types=1);

namespace App\Infrastructure\DI;

use App\Infrastructure\DI\Definition\Argument;
use App\Infrastructure\DI\Definition\Expression\ArrayValue;
use App\Infrastructure\DI\Definition\Expression\ClassConstant;
use App\Infrastructure\DI\Definition\Expression\ClosureFactory;
use App\Infrastructure\DI\Definition\Expression\Expression;
use App\Infrastructure\DI\Definition\Expression\MethodCall;
use App\Infrastructure\DI\Definition\Expression\NewInstance;
use App\Infrastructure\DI\Definition\Expression\Parameter;
use App\Infrastructure\DI\Definition\Expression\Reference;
use App\Infrastructure\DI\Definition\Expression\ScalarValue;
use App\Infrastructure\DI\Definition\Expression\StaticCall;
use App\Infrastructure\DI\Definition\ServiceDefinition;

final class ContainerDumper
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $langDir,
    ) {}

    /**
     * @param list<ServiceDefinition> $definitions
     */
    public function dump(string $namespace, string $className, array $definitions): string
    {
        $methods = array_map(
            fn(ServiceDefinition $definition): string => $this->dumpMethod($definition),
            $definitions
        );

        $methodsCode = implode("\n\n", $methods);
        $templateDir = var_export($this->templateDir, true);
        $langDir = var_export($this->langDir, true);

        return <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

use App\Infrastructure\DI\Container;

final class {$className} extends Container
{
    private const TEMPLATE_DIR = {$templateDir};
    private const LANG_DIR = {$langDir};

{$methodsCode}

    /**
     * @return array<string, string>
     */
    private static function loadMessages(string \$locale): array
    {
        \$file = self::LANG_DIR . '/' . \$locale . '.php';

        if (!is_file(\$file)) {
            throw new \\RuntimeException("Language file not found: {\$file}");
        }

        \$messages = require \$file;

        if (!is_array(\$messages)) {
            throw new \\RuntimeException('Language file must return array.');
        }

        /** @var array<string, string> \$messages */
        return \$messages;
    }
}
PHP;
    }

    private function dumpMethod(ServiceDefinition $definition): string
    {
        $id = var_export($definition->id, true);
        $returnType = '\\' . ltrim($definition->returnType, '\\');
        $methodName = $definition->methodName;
        $factoryCode = $this->renderExpression($definition->factory);

        return <<<PHP
    public function {$methodName}(): {$returnType}
    {
        /** @var {$returnType} */
        return \$this->share({$id}, fn() => {$factoryCode});
    }
PHP;
    }

    private function renderExpression(Expression $expression): string
    {
        if ($expression instanceof Parameter) {
            return $this->renderParameter($expression);
        }

        if ($expression instanceof NewInstance) {
            return $this->renderNewInstance($expression);
        }

        if ($expression instanceof Reference) {
            return $this->renderReference($expression);
        }

        if ($expression instanceof ScalarValue) {
            return var_export($expression->value, true);
        }

        if ($expression instanceof ArrayValue) {
            return $this->renderArrayValue($expression);
        }

        if ($expression instanceof ClassConstant) {
            return $this->renderClassConstant($expression);
        }

        if ($expression instanceof StaticCall) {
            return $this->renderStaticCall($expression);
        }

        if ($expression instanceof MethodCall) {
            return $this->renderMethodCall($expression);
        }

        if ($expression instanceof ClosureFactory) {
            return $this->renderClosureFactory($expression);
        }

        throw new \LogicException('Unsupported expression: ' . $expression::class);
    }

    private function renderParameter(Parameter $expression): string
    {
        return sprintf(
            '$this->getParameter(%s)',
            var_export($expression->name, true)
        );
    }

    private function renderNewInstance(NewInstance $expression): string
    {
        $className = '\\' . ltrim($expression->className, '\\');
        $arguments = $this->renderArguments($expression->arguments);

        return sprintf('new %s(%s)', $className, $arguments);
    }

    private function renderReference(Reference $reference): string
    {
        $methodName = $this->createGetterName($reference->id);

        return sprintf('$this->%s()', $methodName);
    }

    private function renderStaticCall(StaticCall $expression): string
    {
        $arguments = $this->renderArguments($expression->arguments);

        return sprintf(
            '%s::%s(%s)',
            $expression->className,
            $expression->methodName,
            $arguments
        );
    }

    private function renderMethodCall(MethodCall $expression): string
    {
        $target = $this->renderExpression($expression->target);
        $arguments = $this->renderArguments($expression->arguments);

        return sprintf(
            '%s->%s(%s)',
            $target,
            $expression->methodName,
            $arguments
        );
    }

    private function renderClosureFactory(ClosureFactory $expression): string
    {
        $returnType = $expression->returnType !== null
            ? ': \\' . ltrim($expression->returnType, '\\')
            : '';

        return sprintf(
            '(function ()%s {' . "\n%s\n" . '    })()',
            $returnType,
            $this->indentClosureBody($expression->body, 2)
        );
    }

    /**
     * @param list<Argument> $arguments
     */
    private function renderArguments(array $arguments): string
    {
        $items = [];

        foreach ($arguments as $argument) {
            $value = $this->renderExpression($argument->value);

            if ($argument->name !== null) {
                $items[] = $argument->name . ': ' . $value;
                continue;
            }

            $items[] = $value;
        }

        return implode(', ', $items);
    }

    private function renderArrayValue(ArrayValue $expression): string
    {
        $items = array_map(
            fn(Expression $item): string => $this->renderExpression($item),
            $expression->items
        );

        return '[' . implode(', ', $items) . ']';
    }

    private function renderClassConstant(ClassConstant $expression): string
    {
        return $this->renderClassReference($expression->className)
            . '::'
            . $expression->constantName;
    }

    private function renderClassReference(string $className): string
    {
        if ($className === 'self' || $className === 'static' || $className === 'parent') {
            return $className;
        }

        return '\\' . ltrim($className, '\\');
    }

    private function createGetterName(string $id): string
    {
        $shortName = $this->extractShortClassName($id);

        return 'get' . $shortName;
    }

    private function extractShortClassName(string $fqcn): string
    {
        $pos = strrpos($fqcn, '\\');

        if ($pos === false) {
            return $fqcn;
        }

        return substr($fqcn, $pos + 1);
    }

    private function indentClosureBody(string $body, int $level): string
    {
        $indent = str_repeat('    ', $level);
        $lines = explode("\n", trim($body));

        $lines = array_map(
            static fn(string $line): string => $line === '' ? $line : $indent . $line,
            $lines
        );

        return implode("\n", $lines);
    }
}
