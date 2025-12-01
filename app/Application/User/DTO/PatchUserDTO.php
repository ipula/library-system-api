<?php

namespace App\Application\User\DTO;

class PatchUserDTO
{
    public function __construct(
        public int $id,
        public array $data
    ) {}
}
