<?php

namespace App\Domain\Book\Repositories;

use App\Domain\Book\Entities\Book;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookRepository
{
    public function findById(int $id): ?Book;
    public function findByIsbn(string $isbn): ?Book;
    public function save(Book $book): ?Book;
    /**
     * @return Book[]
     */
    public function all(Request $request): LengthAwarePaginator;
    public function delete(int $id): ?bool;
}
