<?php

namespace App\Application\Book\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Domain\Book\Repositories\BookRepository;

class GetBookById
{
    public function __construct(
        private BookRepository $repository
    ) {}
    public function execute(int $id): ?BookDTO
    {
        $book = $this->repository->findById($id);

        if (! $book) {
            return null;
        }

        return BookDTO::fromEntity($book);
    }
}
