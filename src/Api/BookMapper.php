<?php

declare(strict_types=1);

namespace WpIrbis\Api;

use Irbis\MarcRecord;
use WpIrbis\Domain\Book;
use WpIrbis\Domain\SearchRequest;

final class BookMapper
{
    public function map(object $brief, MarcRecord $record, SearchRequest $request): Book
    {
        $category = (string) $record->fm(606, 'A');
        $isbn = $this->extractFirst($record, [[10, 'A'], [10, '']]);
        $bbk = $this->extractFirst($record, [[621, 'A'], [686, 'A'], [686, ''], [621, '']]);

        $book = new Book(
            (string) $brief->mfn,
            (string) $record->fm(200, 'A'),
            (string) ($brief->description ?? ''),
            trim((string) $record->fm(700, 'A') . ' ' . (string) $record->fm(700, 'B')),
            $category,
            $category !== '' ? add_query_arg('irbis_search_category', $category, $request->baseUrl) : '',
            (string) $record->fm(951, 'I'),
            $isbn,
            $bbk,
            $record,
            $brief
        );

        $filtered = apply_filters('wp_irbis/book_data', $book, $record, $brief, $request);

        if ($filtered instanceof Book) {
            return $filtered;
        }

        if (is_array($filtered)) {
            return new Book(
                (string) ($filtered['mfn'] ?? $book->mfn),
                (string) ($filtered['title'] ?? $book->title),
                (string) ($filtered['description'] ?? $book->description),
                (string) ($filtered['author'] ?? $book->author),
                (string) ($filtered['category'] ?? $book->category),
                (string) ($filtered['category_link'] ?? $book->categoryLink),
                (string) ($filtered['cover'] ?? $book->cover),
                (string) ($filtered['isbn'] ?? $book->isbn),
                (string) ($filtered['bbk'] ?? $book->bbk),
                $filtered['record'] ?? $book->record,
                $filtered['brief'] ?? $book->brief
            );
        }

        return $book;
    }

    /**
     * @param array<array{0:int,1:string}> $fields
     */
    private function extractFirst(MarcRecord $record, array $fields): string
    {
        foreach ($fields as [$tag, $code]) {
            $value = trim((string) $record->fm($tag, $code));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }
}
