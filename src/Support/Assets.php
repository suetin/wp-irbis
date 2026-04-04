<?php

declare(strict_types=1);

namespace WpIrbis\Support;

final class Assets
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue(): void
    {
        wp_enqueue_style(
            'wp-irbis',
            WP_IRBIS_URL . 'assets/styles/style.css',
            [],
            WP_IRBIS_VERSION
        );

        wp_enqueue_script(
            'wp-irbis',
            WP_IRBIS_URL . 'assets/js/common.js',
            ['jquery'],
            WP_IRBIS_VERSION,
            true
        );
    }
}
