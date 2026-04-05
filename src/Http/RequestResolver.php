<?php

declare(strict_types=1);

namespace WpIrbis\Http;

use WpIrbis\Domain\SearchRequest;
use WpIrbis\Support\UrlResolver;

final class RequestResolver
{
    public function __construct(private readonly UrlResolver $urlResolver)
    {
    }

    public function searchFromGlobals(array $args = []): SearchRequest
    {
        $query = [
            'search_by' => isset($_GET['irbis_search_by']) ? sanitize_key(wp_unslash($_GET['irbis_search_by'])) : null,
            'search_string' => isset($_GET['irbis_search_string']) ? sanitize_text_field(wp_unslash($_GET['irbis_search_string'])) : null,
            'search_category' => isset($_GET['irbis_search_category']) ? sanitize_text_field(wp_unslash($_GET['irbis_search_category'])) : null,
            'filters' => isset($_GET['irbis_filters']) ? wp_unslash($_GET['irbis_filters']) : null,
        ];

        $args = array_filter(
            $args,
            static fn ($value): bool => $value !== null
        );

        return $this->searchFromArray(array_merge($query, $args));
    }

    public function searchFromArray(array $args = []): SearchRequest
    {
        if (array_key_exists('irbis_filters', $args) && ! array_key_exists('filters', $args)) {
            $args['filters'] = $args['irbis_filters'];
        }

        $normalized = $this->normalizeSearch($args);

        $filtered = apply_filters('wp_irbis/request', $normalized, $args);

        if ($filtered instanceof SearchRequest) {
            return $filtered;
        }

        if (is_array($filtered)) {
            return $this->normalizeSearch($filtered);
        }

        return $normalized;
    }
    private function normalizeSearch(array $args): SearchRequest
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
                'filters' => [],
                'limit' => 10,
                'base_url' => '',
            ]
        );

        $searchBy = in_array($request['search_by'], ['title', 'author', 'keywords'], true)
            ? (string) $request['search_by']
            : 'title';

        return new SearchRequest(
            $searchBy,
            sanitize_text_field((string) $request['search_string']),
            sanitize_text_field((string) $request['search_category']),
            $this->normalizeFilters($request['filters']),
            max(1, (int) $request['limit']),
            $this->urlResolver->resolve((string) $request['base_url'])
        );
    }

    /**
     * @return array<string, string>
     */
    private function normalizeFilters(mixed $filters): array
    {
        if (is_string($filters) && $filters !== '') {
            $decoded = json_decode($filters, true);
            $filters = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($filters)) {
            return [];
        }

        $normalized = [];

        foreach ($filters as $key => $value) {
            $key = sanitize_key((string) $key);
            if ($key === '') {
                continue;
            }

            if (is_scalar($value)) {
                $value = sanitize_text_field((string) $value);
            } else {
                continue;
            }

            if ($value === '') {
                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }
}
