<?php

declare(strict_types=1);

namespace App\Presentation\View;

use App\Infrastructure\Time\Clock;
use App\Presentation\Html\HtmlMinifier;
use RuntimeException;

final class TemplateRenderer
{
    public function __construct(
        private readonly string $templateDir,
        private readonly Clock $clock,
        private readonly HtmlMinifier $minifier,
        private readonly Translator $translator
    ) {}

    /**
     * @param array<string,mixed> $data
     * @throws \Throwable
     */
    public function render(string $template, array $data = []): string
    {
        ob_start();

        try {
            $this->includeTemplate($template, $data);

            $html = ob_get_clean();

            if (!is_string($html)) {
                throw new RuntimeException('Template output buffer did not return string.');
            }

            return $this->minifier->minify($html);

        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    public function t(string $key, mixed ...$params): string
    {
        return $this->translator->t($key, ...$params);
    }

    public function locale(): string
    {
        return $this->translator->locale();
    }

    /**
     * @param array<string,mixed> $data
     */
    public function partial(string $template, array $data = []): void
    {
        $this->includeTemplate($template, $data);
    }

    public function clock(): Clock
    {
        return $this->clock;
    }

    public function e(mixed $value): string
    {
        if (!is_scalar($value) && $value !== null) {
            return '';
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @param array<string,mixed> $data
     */
    private function includeTemplate(string $template, array $data = []): void
    {
        $file = rtrim($this->templateDir, '/\\') . '/' . ltrim($template, '/\\') . '.php';

        if (!is_file($file)) {
            throw new RuntimeException("Template not found: {$file}");
        }

        $view = $this;

        extract($data, EXTR_SKIP);

        require $file;
    }
}
