<?php

namespace App\Application\Book\DTO;

class PatchBookDTO
{
    public function __construct(
        public int $id,
        public array $data
    ) {}
}
