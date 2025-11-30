<?php

namespace App\Infrastructure\Presistence\Eloquent\Repositories;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\DTO\PaginatedBookResponseDTO;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use App\Infrastructure\Presistence\Eloquent\Models\BookModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentBookRepository implements BookRepository
{
    public function __construct(
        private BookModel $model
    ) {}

    public function findById(int $id): ?Book
    {
        $this->model->newQuery()
            ->where('id', $id)
            ->first();
    }

    public function findByTitle(string $title): ?Book
    {
        // TODO: Implement findByTitle() method.
    }

    public function findByIsbn(string $isbn): ?Book
    {
        $this->model->newQuery()
            ->where('isbn', $isbn)
            ->first();
    }

    public function save(Book $book): ?Book
    {
        $newBook = $book->getId()
            ? $this->model->newQuery()->findByIsbn($book->getIsbn())
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
        $models = $this->model->query()->paginate($request->get('perPage'));
        // map Eloquent → Domain Entity
        $mapped = $models->getCollection()->map(callback: function (BookModel $model) {
            return new Book(
                id: $model->id,
                title: $model->title,
                author: $model->author,
                isbn: $model->isbn,
                description: $model->description,
                genres: $model->genres,
                stock: $model->stock
            );
        });

        // replace paginator collection with domain entities
        $models->setCollection($mapped);

        return $models;
    }
}
