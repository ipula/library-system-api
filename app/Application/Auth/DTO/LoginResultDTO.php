<?php

namespace App\Application\Auth\DTO;

use App\Application\User\DTO\UserDTO;

class LoginResultDTO
{
    public function __construct(
        public string $token,
        public UserDTO $userDTO,
        public string $tokenType = 'Bearer',
    ) {}

    public function toArray(): array
    {
        return [
            'token'      => $this->token,
            'token_type' => $this->tokenType,
            'user' => $this->userDTO->toArray(),
        ];
    }
}
