<?php

declare(strict_types=1);

namespace App\Presentation\View;

use App\Infrastructure\Time\Clock;
use App\Presentation\Html\HtmlMinifier;

final class TemplateRenderer
{
    public function __construct(
        private readonly string $templateDir,
        private readonly Clock $clock,
        private readonly ?HtmlMinifier $minifier = null
    ) {}

    /**
     * @param array<string,mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        ob_start();
        $this->includeTemplate($template, $data);

        $html = (string) ob_get_clean();

        if ($this->minifier !== null) {
            $html = $this->minifier->minify($html);
        }

        return $html;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function display(string $template, array $data = []): void
    {
        echo $this->render($template, $data);
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

        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param array<string,mixed> $data
     */
    private function includeTemplate(string $template, array $data = []): void
    {
        $file = rtrim($this->templateDir, '/\\') . '/' . ltrim($template, '/\\') . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException("Template not found: {$file}");
        }

        $view = $this;

        extract($data, EXTR_SKIP);

        require $file;
    }
}
