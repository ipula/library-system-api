<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepository
{
    public function save(User $user): User;

    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;
    public function delete(int $id): ?bool;
    public function all(Request $request): ?LengthAwarePaginator;
}
