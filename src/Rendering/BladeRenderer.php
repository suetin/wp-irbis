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
            do_action('wp_irbis/template_render_error', $exception, $template, $view, $context);

            if (defined('WP_DEBUG') && WP_DEBUG) {
                return sprintf(
                    '<div class="wp-irbis-notice wp-irbis-notice--error">%s</div>',
                    esc_html(sprintf('WP IRBIS Blade error in [%s]: %s', $view, $exception->getMessage()))
                );
            }

            return sprintf(
                '<div class="wp-irbis-notice wp-irbis-notice--error">%s</div>',
                esc_html__('Ошибка рендера шаблона каталога.', 'wp-irbis')
            );
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
                do_action('wp_irbis/template_render_error', $exception, $template, $candidate, []);
                break;
            }
        }

        return '';
    }
}
