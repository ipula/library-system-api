<?php

namespace App\Application\Book\DTO;

use App\Domain\Book\Entities\Book;

class BookDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $author,
        public array $genre,
        public string $isbn,
        public ?string $description,
        public int $stock,
    ) {}

    public static function fromEntity(Book $book): self
    {
        return new self(
            id: $book->getId(),
            title: $book->getTitle(),
            author: $book->getAuthor(),
            genre: $book->getGenres(),
            isbn: $book->getIsbn(),
            description: $book->getDescription(),
            stock: $book->getStock(),
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
