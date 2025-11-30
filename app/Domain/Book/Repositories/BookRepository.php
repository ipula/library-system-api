<?php

namespace App\Domain\Book\Repositories;

use App\Domain\Book\Entities\Book;
use App\Infrastructure\Presistence\Eloquent\Models\BookModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookRepository
{
    public function findById(int $id): ?Book;
    public function findByTitle(string $title): ?Book;
    public function findByIsbn(string $isbn): ?Book;
    public function save(Book $book): ?Book;
    /**
     * @return Book[]
     */
    public function all(Request $request): LengthAwarePaginator;
}
