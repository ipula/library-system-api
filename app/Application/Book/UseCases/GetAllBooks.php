<?php

namespace App\Application\Book\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\DTO\PaginatedBookResponseDTO;
use App\Domain\Book\Repositories\BookRepository;
use Illuminate\Http\Request;

class GetAllBooks
{
    public function __construct(
        private BookRepository $repository
    ) {}

    public function getAll(Request $request): PaginatedBookResponseDTO
    {
        $paginator = $this->repository->all($request);
        $dtoData = $paginator->getCollection()->map(
            fn ($book) => (array) BookDTO::fromEntity($book)
        )->toArray();

        return new PaginatedBookResponseDTO(
            data: $dtoData,
            paginator: $paginator
        );
    }
}
