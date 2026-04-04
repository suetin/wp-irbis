<?php

declare(strict_types=1);

namespace WpIrbis\Domain;

final class CatalogRequest
{
    public function __construct(
        public readonly string $searchBy,
        public readonly string $searchString,
        public readonly string $searchCategory,
        public readonly int $limit,
        public readonly string $baseUrl,
        public readonly bool $showForm,
        public readonly bool $showResults
    ) {
    }

    public function hasQuery(): bool
    {
        return $this->searchCategory !== '' || $this->searchString !== '';
    }

    public function toArray(): array
    {
        return [
            'search_by' => $this->searchBy,
            'search_string' => $this->searchString,
            'search_category' => $this->searchCategory,
            'limit' => $this->limit,
            'base_url' => $this->baseUrl,
            'show_form' => $this->showForm,
            'show_results' => $this->showResults,
        ];
    }
}
