<?php

namespace Tests\Unit\Application\Book\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\UseCases\GetBookById;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use Mockery;
use Tests\TestCase;

class GetBookByIdTest extends TestCase
{
    public function testReturnsBookWhenBookExists()
    {
        $repo = Mockery::mock(BookRepository::class);
        $bookId = 10;

        // Create a domain Book entity (adapt to your constructor if needed)
        $book = Book::create(
            title: 'Test Book',
            author: 'Author Name',
            isbn: '1234567890',
            description: 'Nice book',
            genres: ['fantasy', 'action'],
            stock: 5,
        );
        $book->setId($bookId);

        // Expect repository to be called correctly
        $repo->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn($book);

        $useCase = new GetBookById($repo);
        $result = $useCase->execute($bookId);

        $this->assertInstanceOf(BookDTO::class, $result);

        $this->assertSame($bookId, $result->id);
        $this->assertSame('Test Book', $result->title);
        $this->assertSame('Author Name', $result->author );
    }

    public function testReturnsNullWhenBookNotFound()
    {
        $repo = Mockery::mock(BookRepository::class);
        $bookId = 999;

        $repo->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn(null);

        $useCase = new GetBookById($repo);

        $result = $useCase->execute($bookId);

        $this->assertNull($result);
    }
}
