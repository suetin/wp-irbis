<?php

declare(strict_types=1);

namespace WpIrbis\Domain;

use WP_Error;

final class CatalogResult
{
    /**
     * @param Book[] $items
     */
    public function __construct(
        public readonly array $items,
        public readonly ?WP_Error $error,
        public readonly CatalogRequest $request,
        public readonly bool $hasQuery
    ) {
    }

    public function toArray(): array
    {
        return [
            'items' => array_map(
                static fn (Book $book): array => $book->toArray(),
                $this->items
            ),
            'error' => $this->error,
            'request' => $this->request->toArray(),
            'has_query' => $this->hasQuery,
        ];
    }
}
