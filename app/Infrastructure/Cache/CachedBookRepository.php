<?php

namespace App\Infrastructure\Cache;

use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CachedBookRepository implements BookRepository
{
    public function __construct(
        private BookRepository $inner
    ) {}

    public function findById(int $id): ?Book
    {
        return $this->inner->findById($id);
    }

    public function findByIsbn(string $isbn): ?Book
    {
        return $this->inner->findByIsbn($isbn);
    }

    public function save(Book $book): ?Book
    {
        $saved = $this->inner->save($book);
        Cache::tags(['books'])->flush();
        return $saved;
    }

    public function all(Request $request): LengthAwarePaginator
    {
        $key = 'books:all:' . md5(json_encode($request->query()));

        \Log::info('CachedBookRepository key', [
            'key'   => $key,
            'query' => $request->query(),
        ]);
        return Cache::store('redis')->tags(['books'])->remember(
            $key,
            now()->addMinutes(20),
            fn () => $this->inner->all($request)
        );
    }

    public function delete(int $id): ?bool
    {
        $result = $this->inner->delete($id);
        Cache::tags(['books'])->flush();
        return $result;
    }
}
