<?php

declare(strict_types=1);

namespace WpIrbis\Rendering;

final class TemplateRenderer
{
    private string $pluginTemplateDir;

    public function __construct(string $pluginTemplateDir)
    {
        $this->pluginTemplateDir = untrailingslashit($pluginTemplateDir);
    }

    public function render(string $template, array $context = []): string
    {
        $context = apply_filters('wp_irbis/template_context', $context, $template, '');

        $bladeView = $this->locateBladeView($template);
        if ($bladeView !== '' && function_exists('view')) {
            return (string) view($bladeView, $context)->render();
        }

        $path = $this->locatePhpTemplate($template);
        if ($path === '') {
            return '';
        }

        $context = apply_filters('wp_irbis/template_context', $context, $template, $path);

        ob_start();
        include $path;

        return (string) ob_get_clean();
    }

    public function locatePhpTemplate(string $template): string
    {
        $template = trim($template, '/');
        $filename = $template . '.php';
        $paths = apply_filters(
            'wp_irbis/template_php_paths',
            $this->defaultPhpThemePaths(),
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

        $fallback = $this->pluginTemplateDir . '/' . $filename;

        return file_exists($fallback) ? $fallback : '';
    }

    public function locateBladeView(string $template): string
    {
        $template = trim($template, '/');
        $candidates = apply_filters(
            'wp_irbis/template_blade_views',
            $this->defaultBladeViews($template),
            $template
        );

        foreach ($candidates as $candidate) {
            $candidate = is_string($candidate) ? trim($candidate) : '';
            if ($candidate === '') {
                continue;
            }

            if ($this->bladeViewExists($candidate)) {
                return $candidate;
            }
        }

        return '';
    }

    private function defaultPhpThemePaths(): array
    {
        return array_values(array_unique(array_filter([
            get_stylesheet_directory() . '/irbis',
            get_template_directory() . '/irbis',
        ])));
    }

    private function defaultBladeViews(string $template): array
    {
        return [
            'irbis.' . str_replace('/', '.', $template),
        ];
    }

    private function bladeViewExists(string $view): bool
    {
        if (function_exists('view')) {
            try {
                return (bool) view()->exists($view);
            } catch (\Throwable $exception) {
                return false;
            }
        }

        return false;
    }
}
