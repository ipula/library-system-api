<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\DTO\LoginResultDTO;
use App\Application\Auth\DTO\LoginUserDTO;
use App\Application\User\DTO\UserDTO;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class LoginUser
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function execute(LoginUserDTO $input): LoginResultDTO
    {
        $user = $this->userRepository->findModelByEmail($input->email);

        if (! $user || ! Hash::check($input->password, $user->password)) {
            throw new UnauthorizedHttpException('', 'Invalid credentials.');
        }

        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;
        $userEntity = User::register(
            name: $user->name,
            email:$user->email,
            passwordHash: $user->password,
        );
        $userEntity->setId($user->id);
        $userDto = UserDTO::fromEntity($userEntity);

        return new LoginResultDTO($token,userDTO: $userDto);
    }
}
