<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTO\UserDTO;
use App\Domain\User\Repositories\UserRepository;

class GetUserById
{
    public function __construct(
        private UserRepository $repository
    ) {}
    public function execute(int $id): ?UserDTO
    {
        $user = $this->repository->findById($id);

        if (! $user) {
            return null;
        }

        return UserDTO::fromEntity($user);
    }
}
