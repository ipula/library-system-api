<?php

namespace App\Application\User\UseCases;

use App\Application\Common\DTO\PaginatedDTO;
use App\Application\User\DTO\UserDTO;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Http\Request;

class FetchAllUser
{
    public function __construct(
        private UserRepository $repository
    ) {}
    public function getAll(Request $request): PaginatedDTO
    {
        $paginator = $this->repository->all($request);
        $dtoData = $paginator->getCollection()->map(
            fn ($book) => (array) UserDTO::fromEntity($book)
        )->toArray();

        return new PaginatedDTO(
            data: $dtoData,
            paginator: $paginator
        );
    }
}
