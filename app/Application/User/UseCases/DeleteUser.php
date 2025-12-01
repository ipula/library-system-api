<?php

namespace App\Application\User\UseCases;


use App\Domain\User\Repositories\UserRepository;


class DeleteUser
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function execute(int $id): ?bool
    {
        $user = $this->repository->findById($id);
        if (! $user) {
            return null;
        }
        $this->repository->delete($id);
        return true;
    }
}
