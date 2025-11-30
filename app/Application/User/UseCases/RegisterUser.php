<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTO\RegisterUserInput;
use App\Application\User\DTO\UserDTO;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    public function __construct( private readonly UserRepository $userRepository)
    {
    }
    public function execute(RegisterUserInput $request): UserDTO
    {
        // Hashing can live in the application layer
        $passwordHash = Hash::make($request->password);

        // Here is your DDD-style named constructor
        $user = User::register(
            name: $request->name,
            email: $request->email,
            passwordHash: $passwordHash,
        );
        $this->userRepository->save($user);
        return UserDTO::fromEntity($user);
    }
}
