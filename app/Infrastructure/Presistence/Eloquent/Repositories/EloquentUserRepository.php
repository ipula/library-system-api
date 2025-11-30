<?php

namespace App\Infrastructure\Presistence\Eloquent\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\Presistence\Eloquent\Models\UserModel;

class EloquentUserRepository implements UserRepository
{
    public function __construct(
        private UserModel $model
    ) {}

    public function save(User $user): User
    {
        $eloquent = $user->getId()
            ? $this->model->newQuery()->findOrFail($user->getId())
            : new UserModel();

        $eloquent->name = $user->getName();
        $eloquent->email = $user->getEmail();
        $eloquent->password = $user->getPasswordHash();
        $eloquent->save();

        if (!$user->getId()) {
            $user->setId($eloquent->id);
        }

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        $user = $this->model->newQuery()
            ->where('email', $email)
            ->first();

        if (!$user) {
            return null;
        }

        return new User(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            passwordHash: $user->password,
        );
    }
}
