<?php

namespace App\Application\Common\DTO;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginatedDTO
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
