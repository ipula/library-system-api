<?php

namespace App\Application\Book\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\DTO\PatchBookDTO;
use App\Domain\Book\Repositories\BookRepository;

class PatchBook
{
    public function __construct(
        private BookRepository $repository
    ) {}

    public function execute(PatchBookDTO $bookDTO): ?BookDTO
    {
        $book = $this->repository->findById($bookDTO->id);
        if (! $book) {
            return null;
        }

        $data = $bookDTO->data;

        $map = [
            'title'  => 'setTitle',
            'author' => 'setAuthor',
            'isbn'   => 'setIsbn',
            'genres' => 'setGenre',
            'stock'  => 'setStock',
        ];

        foreach ($map as $field => $setter) {
            if (array_key_exists($field, $data)) {
                $book->{$setter}($data[$field]);
            }
        }
        $this->repository->save($book);
        return BookDTO::fromEntity($book);
    }
}
