<?php

namespace App\Application\Book\DTO;

class CreateBookInput
{
    public function __construct(
        public string $title,
        public string $author,
        public array $genre,
        public string $isbn,
        public ?string $description,
        public int $stock,
    ) {}
}
