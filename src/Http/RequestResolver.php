<?php

declare(strict_types=1);

namespace WpIrbis\Http;

use WpIrbis\Domain\CatalogRequest;
use WpIrbis\Support\UrlResolver;

final class RequestResolver
{
    public function __construct(private readonly UrlResolver $urlResolver)
    {
    }

    public function fromGlobals(array $args = []): CatalogRequest
    {
        $query = [
            'search_by' => isset($_GET['irbis_search_by']) ? sanitize_key(wp_unslash($_GET['irbis_search_by'])) : null,
            'search_string' => isset($_GET['irbis_search_string']) ? sanitize_text_field(wp_unslash($_GET['irbis_search_string'])) : null,
            'search_category' => isset($_GET['irbis_search_category']) ? sanitize_text_field(wp_unslash($_GET['irbis_search_category'])) : null,
            'base_url' => null,
        ];

        $args = array_filter(
            $args,
            static fn ($value): bool => $value !== null
        );

        return $this->fromArray(array_merge($query, $args));
    }

    public function fromArray(array $args = []): CatalogRequest
    {
        $normalized = $this->normalize($args);

        $filtered = apply_filters('wp_irbis/request', $normalized, $args);

        if ($filtered instanceof CatalogRequest) {
            return $filtered;
        }

        if (is_array($filtered)) {
            return $this->normalize($filtered);
        }

        return $normalized;
    }

    private function normalize(array $args): CatalogRequest
    {
        $args = array_filter(
            $args,
            static fn ($value): bool => $value !== null
        );

        $request = wp_parse_args(
            $args,
            [
                'search_by' => 'title',
                'search_string' => '',
                'search_category' => '',
                'limit' => 10,
                'base_url' => '',
                'show_form' => true,
                'show_results' => true,
            ]
        );

        $searchBy = in_array($request['search_by'], ['title', 'author', 'keywords'], true)
            ? (string) $request['search_by']
            : 'title';

        return new CatalogRequest(
            $searchBy,
            sanitize_text_field((string) $request['search_string']),
            sanitize_text_field((string) $request['search_category']),
            max(1, (int) $request['limit']),
            $this->urlResolver->resolve((string) $request['base_url']),
            wp_validate_boolean($request['show_form']),
            wp_validate_boolean($request['show_results'])
        );
    }
}
