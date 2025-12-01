<?php

namespace App\Infrastructure\Presistence\Eloquent\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\UserHasActiveRentalsException;
use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\Presistence\Eloquent\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepository
{
    public function __construct(
        private UserModel $model
    ) {}

    public function save(User $user): User
    {
        $newUser = $user->getId()
            ? $this->model->newQuery()->findOrFail($user->getId())
            : new UserModel();

        $newUser->name = $user->getName();
        $newUser->email = $user->getEmail();
        $newUser->password = $user->getPasswordHash();
        $newUser->save();

        if (!$user->getId()) {
            $user->setId($newUser->id);
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

        return $this->toEntity($user);
    }

    public function delete(int $id): ?bool
    {
        $hasActive = $this->model->newQuery()
            ->where('id', $id)
            ->hasActiveRental()
            ->exists();

        if ($hasActive) {
            throw new UserHasActiveRentalsException();
        }
        return $this->model->query()->where('id',$id)->delete();
    }

    public function all(Request $request): ?LengthAwarePaginator
    {
        $models = $this->model->query()->paginate($request->get('perPage'));
        // map Eloquent → Domain Entity
        $mapped = $models->getCollection()->map(callback: function (UserModel $model) {
            return $this->toEntity($model);
        });

        // replace paginator collection with domain entities
        $models->setCollection($mapped);

        return $models;
    }

    public function findById(int $id): ?User
    {
        $user = $this->model->newQuery()->find($id);

        if (!$user) {
            return null;
        }

        return $this->toEntity($user);
    }

    public function findModelByEmail(string $email): ?UserModel
    {
        return $this->model->newQuery()
            ->where('email', $email)
            ->first();
    }

    private function toEntity($model): User
    {
        return new User(
            id: $model->id,
            name: $model->name,
            email: $model->email,
            passwordHash: $model->password,
        );
    }
}
