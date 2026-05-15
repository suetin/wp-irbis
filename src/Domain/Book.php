<?php

declare(strict_types=1);

namespace WpIrbis\Domain;

final class Book
{
    public function __construct(
        public readonly string $mfn,
        public readonly string $title,
        public readonly string $description,
        public readonly string $author,
        public readonly string $category,
        public readonly string $categoryLink,
        public readonly string $cover,
        public readonly string $isbn,
        public readonly string $bbk,
        public readonly object $record,
        public readonly object $brief
    ) {
    }

    public function toArray(): array
    {
        return [
            'mfn' => $this->mfn,
            'title' => $this->title,
            'description' => $this->description,
            'author' => $this->author,
            'category' => $this->category,
            'category_link' => $this->categoryLink,
            'cover' => $this->cover,
            'isbn' => $this->isbn,
            'bbk' => $this->bbk,
        ];
    }
}
