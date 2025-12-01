<?php

namespace App\Infrastructure\Presistence\Eloquent\Repositories;

use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use App\Infrastructure\Presistence\Eloquent\Models\BookModel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentBookRepository implements BookRepository
{
    public function __construct(
        private BookModel $model
    ) {}

    public function findById(int $id): ?Book
    {
        $book = $this->model->newQuery()->find($id);
        if (!$book) {
            return null;
        }
        return $this->toEntity($book);
    }

    public function findByIsbn(string $isbn): ?Book
    {
        $book = $this->model->newQuery()
            ->where('isbn', $isbn)
            ->first();
        return $this->toEntity($book);
    }

    public function save(Book $book): ?Book
    {
        $newBook = $book->getId()
            ? $this->model->newQuery()->findOrFail($book->getId())
            : new BookModel();

        $newBook->title = $book->getTitle();
        $newBook->isbn = $book->getIsbn();
        $newBook->description = $book->getDescription();
        $newBook->genres = $book->getGenres();
        $newBook->author = $book->getAuthor();
        $newBook->stock = $book->getStock();
        $newBook->save();

        if (!$book->getId()) {
            $book->setId($newBook->id);
        }

        return $book;
    }

    public function all(Request $request): LengthAwarePaginator
    {
        $query = $this->model->query();
        $sortBy = $request->get('sortBy') ? $request->get('sortBy') : 'id';
        $orderBy = $request->get('orderBy') ? $request->get('orderBy') : 'asc';
        $genre = $request->get('genre') ? $request->get('genre') : null;
        $search = $request->get('search') ? $request->get('search') : null;

        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('author', 'like', '%' . $search . '%')
                    ->orWhere('isbn', 'like', '%' . $search . '%')
                    ->orWhereJsonContains('genres', $search);
            });
        }

        $orderQuery = match ($sortBy) {
            'title', 'author', 'isbn' =>
            $query->orderBy($sortBy, $orderBy),

            'availability' =>
            $query->orderByRaw("CASE WHEN stock > 0 THEN 1 ELSE 0 END $orderBy"),

            default =>
            $query->orderBy('title', 'asc'),
        };
        if($genre){
            $query->whereJsonContains('genres', $genre);
        }
        $models = $orderQuery->paginate($request->get('perPage'));
        // map Eloquent → Domain Entity
        $mapped = $models->getCollection()->map(callback: function (BookModel $model) {
            return $this->toEntity($model);
        });

        // replace paginator collection with domain entities
        $models->setCollection($mapped);

        return $models;
    }

    public function delete(int $id): ?bool
    {
        return  $this->model->query()->where('id',$id)->delete();
    }

    private function toEntity($model): Book
    {
        return new Book(
            id: $model->id,
            title: $model->title,
            author: $model->author,
            isbn: $model->isbn,
            description: $model->description,
            genres: $model->genres,
            stock: $model->stock
        );
    }
}
