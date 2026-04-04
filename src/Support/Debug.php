<?php

declare(strict_types=1);

namespace WpIrbis\Support;

final class Debug
{
    public static function isDevelopment(): bool
    {
        if (function_exists('wp_get_environment_type')) {
            return wp_get_environment_type() === 'development';
        }

        return defined('WP_ENV') && WP_ENV === 'development';
    }
}
