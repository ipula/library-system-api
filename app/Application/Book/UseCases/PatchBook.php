<?php

namespace App\Application\Book\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\DTO\PatchBookDTO;
use App\Domain\Book\Entities\Book;
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

        if (array_key_exists('title', $data)) {
            $book->setTitle($data['title']);
        }

        if (array_key_exists('author', $data)) {
            $book->setAuthor($data['author']);
        }

        if (array_key_exists('isbn', $data)) {
            $book->setIsbn($data['isbn']);
        }

        if (array_key_exists('genres', $data)) {
            $book->setGenre($data['genres']);
        }

        if (array_key_exists('stock', $data)) {
            $book->setStock($data['stock']);
        }
        $this->repository->save($book);
        return BookDTO::fromEntity($book);
    }
}
