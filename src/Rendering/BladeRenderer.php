<?php

declare(strict_types=1);

namespace WpIrbis\Rendering;

use WpIrbis\Contracts\RendererStrategy;

final class BladeRenderer implements RendererStrategy
{
    public function render(string $template, array $context = []): string
    {
        $view = $this->locate($template);
        if ($view === '' || ! function_exists('view')) {
            return '';
        }

        try {
            return (string) view($view, $context)->render();
        } catch (\Throwable $exception) {
            return '';
        }
    }

    private function locate(string $template): string
    {
        $template = trim($template, '/');
        $candidates = apply_filters(
            'wp_irbis/template_blade_views',
            ['irbis.' . str_replace('/', '.', $template)],
            $template
        );

        foreach ($candidates as $candidate) {
            $candidate = is_string($candidate) ? trim($candidate) : '';
            if ($candidate === '' || ! function_exists('view')) {
                continue;
            }

            try {
                if ((bool) view()->exists($candidate)) {
                    return $candidate;
                }
            } catch (\Throwable $exception) {
                return '';
            }
        }

        return '';
    }
}
