<?php

declare(strict_types=1);

namespace WpIrbis\Support;

final class Assets
{
    private bool $registered = false;

    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
    }

    public function registerAssets(): void
    {
        wp_register_style(
            'wp-irbis',
            WP_IRBIS_URL . 'assets/styles/style.css',
            [],
            WP_IRBIS_VERSION
        );

        wp_register_script(
            'wp-irbis',
            WP_IRBIS_URL . 'assets/js/common.js',
            ['jquery'],
            WP_IRBIS_VERSION,
            true
        );

        $this->registered = true;
    }

    public function enqueue(): void
    {
        if (! $this->registered && function_exists('wp_styles')) {
            $this->registerAssets();
        }

        wp_enqueue_style('wp-irbis');
        wp_enqueue_script('wp-irbis');
    }
}
