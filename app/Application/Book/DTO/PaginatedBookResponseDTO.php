<?php

namespace App\Application\Book\DTO;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginatedBookResponseDTO
{
    public function __construct(
        public array $data,
        public LengthAwarePaginator $paginator
    ) {}

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'pagination' => [
                'total'         => $this->paginator->total(),
                'per_page'      => $this->paginator->perPage(),
                'current_page'  => $this->paginator->currentPage(),
                'last_page'     => $this->paginator->lastPage(),
            ],
        ];
    }
}
