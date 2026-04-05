<?php

declare(strict_types=1);

namespace WpIrbis\Api;

use WpIrbis\Domain\CatalogResult;
use WpIrbis\Http\RequestResolver;

final class Catalog
{
    public function __construct(
        private readonly SearchService $search,
        private readonly RequestResolver $requests
    ) {
    }

    public function search(array $args = []): array
    {
        return $this->searchResult($args)->toArray();
    }

    public function searchResult(array $args = []): CatalogResult
    {
        $request = $this->requests->searchFromArray($args);

        return $this->search->search($request);
    }
}
