<?php

declare(strict_types=1);

namespace WpIrbis\Api;

use Irbis\Search;
use Irbis\SearchParameters;
use WP_Error;
use WpIrbis\Domain\CatalogRequest;
use WpIrbis\Domain\CatalogResult;
use WpIrbis\Domain\Book;
use WpIrbis\Exceptions\IrbisException;
use const Irbis\BRIEF_FORMAT;

final class SearchService
{
    public function __construct(
        private readonly IrbisGateway $gateway,
        private readonly BookMapper $books
    ) {
    }

    public function search(CatalogRequest $request): CatalogResult
    {
        if (! $request->hasQuery()) {
            return new CatalogResult([], null, $request, false, $this->makeDebugPayload($request));
        }

        $parameters = null;
        try {
            $parameters = $this->buildParameters($request);
            $foundBooks = $this->gateway->search($parameters);
            $items = $this->mapItems($foundBooks, $request);
        } catch (IrbisException $exception) {
            return new CatalogResult(
                [],
                new WP_Error($exception->errorCodeName(), $exception->getMessage()),
                $request,
                true,
                $this->makeDebugPayload($request, $parameters, [], 0, false, $exception)
            );
        }

        $result = new CatalogResult(
            $items,
            null,
            $request,
            true,
            $this->makeDebugPayload($request, $parameters, $foundBooks, count($items), true)
        );
        $filtered = apply_filters('wp_irbis/search_result', $result, $request, $parameters);

        return $filtered instanceof CatalogResult ? $filtered : $result;
    }

    private function buildParameters(CatalogRequest $request): SearchParameters
    {
        $parameters = new SearchParameters();
        $parameters->format = BRIEF_FORMAT;
        $parameters->numberOfRecords = $request->limit;
        $parameters->expression = $this->buildExpression($request);

        $filtered = apply_filters('wp_irbis/search_parameters', $parameters, $request);

        return $filtered instanceof SearchParameters ? $filtered : $parameters;
    }

    /**
     * @param array<int, object> $foundBooks
     * @return Book[]
     * @throws IrbisException
     */
    private function mapItems(array $foundBooks, CatalogRequest $request): array
    {
        $mfns = [];
        foreach ($foundBooks as $brief) {
            $mfns[] = (int) ($brief->mfn ?? 0);
        }

        $records = $this->gateway->readRecords(array_values(array_filter($mfns)));
        $items = [];

        foreach ($foundBooks as $brief) {
            $mfn = (int) ($brief->mfn ?? 0);
            if ($mfn <= 0 || ! isset($records[$mfn])) {
                continue;
            }

            $items[] = $this->books->map($brief, $records[$mfn], $request);
        }

        return $items;
    }

    private function buildExpression(CatalogRequest $request): string
    {
        if ($request->searchCategory !== '') {
            return (string) Search::equals('S=', $request->searchCategory . '$');
        }

        if ($request->searchString === '') {
            return '';
        }

        $map = [
            'title' => 'T=',
            'author' => 'A=',
            'keywords' => 'K=',
        ];

        return (string) Search::equals(
            $map[$request->searchBy] ?? 'T=',
            $request->searchString . '$'
        );
    }

    private function makeDebugPayload(
        CatalogRequest $request,
        ?SearchParameters $parameters = null,
        array $foundBooks = [],
        int $mappedItems = 0,
        bool $connectionOk = false,
        ?IrbisException $exception = null
    ): array {
        return [
            'enabled' => \WpIrbis\Support\Debug::isDevelopment(),
            'environment' => function_exists('wp_get_environment_type') ? wp_get_environment_type() : (defined('WP_ENV') ? WP_ENV : 'unknown'),
            'connection_ok' => $connectionOk,
            'search_by' => $request->searchBy,
            'search_string' => $request->searchString,
            'search_category' => $request->searchCategory,
            'base_url' => $request->baseUrl,
            'limit' => $request->limit,
            'expression' => $parameters instanceof SearchParameters ? (string) $parameters->expression : '',
            'found_count' => count($foundBooks),
            'rendered_count' => $mappedItems,
            'error_code' => $exception?->errorCodeName(),
            'error_message' => $exception?->getMessage(),
        ];
    }
}
