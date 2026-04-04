<?php

declare(strict_types=1);

namespace WpIrbis\Support;

final class UrlResolver
{
    public function resolve(?string $preferred = null): string
    {
        if (is_string($preferred) && $preferred !== '') {
            return $preferred;
        }

        if (function_exists('get_queried_object_id')) {
            $objectId = (int) get_queried_object_id();
            if ($objectId > 0 && function_exists('get_permalink')) {
                $permalink = get_permalink($objectId);
                if (is_string($permalink) && $permalink !== '') {
                    return $permalink;
                }
            }
        }

        $requestUri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '/';
        $path = (string) wp_parse_url($requestUri, PHP_URL_PATH);

        if ($path === '') {
            return home_url('/');
        }

        return home_url($path);
    }
}
