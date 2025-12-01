<?php

namespace App\Application\Auth\DTO;

class ResetPasswordDTO
{
    public function __construct(
        public string $email,
        public string $token,
        public string $password,
        public string $passwordConfirmation,
    ) {}
}
