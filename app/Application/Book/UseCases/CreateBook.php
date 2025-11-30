<?php

namespace App\Application\Book\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\DTO\CreateBookInput;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;

class CreateBook
{
    public function __construct(
        private BookRepository $repository
    ) {}

    public function execute(CreateBookInput $request): BookDTO
    {
        $book = Book::create(
            title: $request->title,
            author: $request->author,
            isbn: $request->isbn,
            description: $request->description,
            genres: $request->genre,
            stock: $request->stock, // 👈 pass stock to domain
        );

        $this->repository->save($book);
        return BookDTO::fromEntity($book);
    }
}
