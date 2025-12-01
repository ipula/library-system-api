<?php

namespace App\Application\Auth\DTO;

class LoginResultDTO
{
    public function __construct(
        public string $token,
        public string $tokenType = 'Bearer',
    ) {}

    public function toArray(): array
    {
        return [
            'token'      => $this->token,
            'token_type' => $this->tokenType,
        ];
    }
}
