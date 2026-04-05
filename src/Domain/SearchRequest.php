<?php

declare(strict_types=1);

namespace WpIrbis\Domain;

final class SearchRequest
{
    /**
     * @param array<string, string> $filters
     */
    public function __construct(
        public readonly string $searchBy,
        public readonly string $searchString,
        public readonly string $searchCategory,
        public readonly array $filters,
        public readonly int $limit,
        public readonly string $baseUrl
    ) {
    }

    public function hasQuery(): bool
    {
        return $this->searchCategory !== ''
            || $this->searchString !== ''
            || $this->filters !== [];
    }

    public function toArray(): array
    {
        return [
            'search_by' => $this->searchBy,
            'search_string' => $this->searchString,
            'search_category' => $this->searchCategory,
            'filters' => $this->filters,
            'limit' => $this->limit,
            'base_url' => $this->baseUrl,
        ];
    }
}
