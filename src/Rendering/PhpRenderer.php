<?php

declare(strict_types=1);

namespace WpIrbis\Rendering;

use WpIrbis\Contracts\RendererStrategy;

final class PhpRenderer implements RendererStrategy
{
    public function __construct(private readonly string $pluginTemplateDir)
    {
    }

    public function render(string $template, array $context = []): string
    {
        $path = $this->locate($template);
        if ($path === '') {
            return '';
        }

        ob_start();
        include $path;

        return (string) ob_get_clean();
    }

    private function locate(string $template): string
    {
        $template = trim($template, '/');
        $filename = $template . '.php';
        $paths = apply_filters(
            'wp_irbis/template_php_paths',
            [
                get_stylesheet_directory() . '/irbis',
                get_template_directory() . '/irbis',
            ],
            $template,
            $filename
        );

        foreach ($paths as $basePath) {
            $basePath = is_string($basePath) ? untrailingslashit($basePath) : '';
            if ($basePath === '') {
                continue;
            }

            $candidate = $basePath . '/' . $filename;
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        $fallback = untrailingslashit($this->pluginTemplateDir) . '/' . $filename;

        return file_exists($fallback) ? $fallback : '';
    }
}
