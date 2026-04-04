<?php

declare(strict_types=1);

namespace WpIrbis\Api;

use Irbis\Search;
use Irbis\SearchParameters;
use WP_Error;
use WpIrbis\Rendering\TemplateRenderer;
use const Irbis\BRIEF_FORMAT;

final class Catalog
{
    private ConnectionFactory $connections;

    private TemplateRenderer $templates;

    public function __construct(ConnectionFactory $connections, TemplateRenderer $templates)
    {
        $this->connections = $connections;
        $this->templates = $templates;
    }

    public function render(array $args = []): string
    {
        $request = $this->normalizeRequest($args);
        $result = $this->search($request);

        return $this->templates->render(
            'catalog',
            [
                'request' => $request,
                'result' => $result,
            ]
        );
    }

    public function search(array $args = []): array
    {
        $request = $this->normalizeRequest($args);
        $hasQuery = $this->hasQuery($request);

        if (! $hasQuery) {
            return [
                'items' => [],
                'error' => null,
                'request' => $request,
                'has_query' => false,
            ];
        }

        $connection = $this->connections->make();

        if ($connection instanceof WP_Error) {
            return [
                'items' => [],
                'error' => $connection,
                'request' => $request,
                'has_query' => true,
            ];
        }

        $parameters = new SearchParameters();
        $parameters->format = BRIEF_FORMAT;
        $parameters->numberOfRecords = (int) $request['limit'];
        $parameters->expression = $this->buildExpression($request);

        $parameters = apply_filters('wp_irbis/search_parameters', $parameters, $request);

        $foundBooks = $connection->searchEx($parameters);
        $items = [];

        foreach ($foundBooks as $book) {
            $record = $connection->readRecord($book->mfn);
            $category = (string) $record->fm(606, 'A');

            $item = [
                'mfn' => (string) $book->mfn,
                'title' => (string) $record->fm(200, 'A'),
                'description' => (string) $book->description,
                'author' => trim((string) $record->fm(700, 'A') . ' ' . (string) $record->fm(700, 'B')),
                'category' => $category,
                'category_link' => $category !== '' ? add_query_arg('irbis_search_category', $category, $request['base_url']) : '',
                'cover' => (string) $record->fm(951, 'I'),
                'record' => $record,
                'brief' => $book,
            ];

            $items[] = apply_filters('wp_irbis/book_data', $item, $record, $book, $request);
        }

        $result = [
            'items' => $items,
            'error' => null,
            'request' => $request,
            'has_query' => true,
        ];

        return apply_filters('wp_irbis/search_result', $result, $request, $parameters);
    }

    private function normalizeRequest(array $args): array
    {
        $args = array_filter(
            $args,
            static fn ($value): bool => $value !== null
        );

        $defaults = [
            'search_by' => isset($_GET['irbis_search_by']) ? sanitize_key(wp_unslash($_GET['irbis_search_by'])) : 'title',
            'search_string' => isset($_GET['irbis_search_string']) ? sanitize_text_field(wp_unslash($_GET['irbis_search_string'])) : '',
            'search_category' => isset($_GET['irbis_search_category']) ? sanitize_text_field(wp_unslash($_GET['irbis_search_category'])) : '',
            'limit' => 10,
            'base_url' => get_permalink() ?: home_url('/'),
            'show_form' => true,
            'show_results' => true,
        ];

        $request = wp_parse_args($args, $defaults);
        $request['limit'] = max(1, (int) $request['limit']);
        $request['show_form'] = wp_validate_boolean($request['show_form']);
        $request['show_results'] = wp_validate_boolean($request['show_results']);
        $request['search_by'] = in_array($request['search_by'], ['title', 'author', 'keywords'], true)
            ? $request['search_by']
            : 'title';

        return apply_filters('wp_irbis/request', $request, $args);
    }

    private function buildExpression(array $request): string
    {
        if (! empty($request['search_category'])) {
            return Search::equals('S=', $request['search_category'] . '$');
        }

        if (empty($request['search_string'])) {
            return '';
        }

        $map = [
            'title' => 'T=',
            'author' => 'A=',
            'keywords' => 'K=',
        ];

        $prefix = $map[$request['search_by']] ?? $map['title'];

        return Search::equals($prefix, $request['search_string'] . '$');
    }

    private function hasQuery(array $request): bool
    {
        return $request['search_category'] !== '' || $request['search_string'] !== '';
    }
}
