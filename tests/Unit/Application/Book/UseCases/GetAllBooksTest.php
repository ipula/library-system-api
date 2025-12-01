<?php

namespace Tests\Unit\Application\Book\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\DTO\CreateBookInput;
use App\Application\Book\UseCases\CreateBook;
use App\Application\Book\UseCases\GetAllBooks;
use App\Application\Common\DTO\PaginatedDTO;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class GetAllBooksTest extends TestCase
{
    public function testGetAllBooksReturnsPaginatedDtoWithMappedBooks()
    {
        $repo = Mockery::mock(BookRepository::class);

        $request = new Request([
            'page'    => 1,
            'perPage' => 2,
            'sortBy'  => 'title',
            'orderBy' => 'asc',
        ]);


        $book1 = Book::create(
            title: 'Test Book 1',
            author: 'Author 1',
            isbn: '1111111111',
            description: 'First book',
            genres: ['fantasy'],
            stock: 3,
        );
        $book1->setId(1);

        $book2 = Book::create(
            title: 'Test Book 2',
            author: 'Author 2',
            isbn: '2222222222',
            description: 'Second book',
            genres: ['sci-fi'],
            stock: 5,
        );
        $book2->setId(2);

        $collection = collect([$book1, $book2]);

        $paginator = new LengthAwarePaginator(
            items: $collection,
            total: 2,
            perPage: 2,
            currentPage: 1
        );

        $repo->shouldReceive('all')
            ->once()
            ->with($request)
            ->andReturn($paginator);

        $useCase = new GetAllBooks($repo);

        $result = $useCase->getAll($request);
        $this->assertInstanceOf(PaginatedDTO::class, $result);

        $this->assertIsArray($result->data);
        $this->assertCount(2, $result->data);

        $first = $result->data[0];
        $this->assertSame('Test Book 1', $first['title'] ?? null);
        $this->assertSame('Author 1', $first['author'] ?? null);
        $this->assertSame('1111111111', $first['isbn'] ?? null);

        // 3) Paginator is preserved with correct meta
        $this->assertSame(2, $result->paginator->total());
        $this->assertSame(2, $result->paginator->perPage());
        $this->assertSame(1, $result->paginator->currentPage());
    }

    public function testGetAllBooksHandlesEmptyResult()
    {
        $repo = Mockery::mock(BookRepository::class);

        $request = new Request([
            'page'    => 1,
            'perPage' => 10,
        ]);

        $collection = collect([]);
        $paginator = new LengthAwarePaginator(
            items: $collection,
            total: 0,
            perPage: 10,
            currentPage: 1
        );

        $repo->shouldReceive('all')
            ->once()
            ->with($request)
            ->andReturn($paginator);

        $useCase = new GetAllBooks($repo);

        $result = $useCase->getAll($request);

        $this->assertInstanceOf(PaginatedDTO::class, $result);
        $this->assertIsArray($result->data);
        $this->assertCount(0, $result->data);
        $this->assertSame(0, $result->paginator->total());
    }
}
