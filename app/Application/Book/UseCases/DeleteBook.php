<?php

namespace App\Application\Book\UseCases;

use App\Domain\Book\Repositories\BookRepository;

class DeleteBook
{
    public function __construct(
        private BookRepository $repository
    ) {}

    public function execute(int $id): ?bool
    {
        $book = $this->repository->findById($id);
        if (! $book) {
            return null;
        }
        $this->repository->delete($id);
        return true;
    }
}
