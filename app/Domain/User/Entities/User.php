<?php

namespace App\Domain\User\Entities;

class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private string $passwordHash,
    ) {}

    public static function register(string $name, string $email, string $passwordHash): self
    {
        return new self(
            id: null,
            name: $name,
            email: $email,
            passwordHash: $passwordHash,
        );
    }

    public function getId(): ?int   { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPasswordHash(): string{ return $this->passwordHash; }
}
