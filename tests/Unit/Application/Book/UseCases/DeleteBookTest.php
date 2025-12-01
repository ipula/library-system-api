<?php

namespace Tests\Unit\Application\Book\UseCases;

use App\Application\Book\UseCases\DeleteBook;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use Mockery;
use Tests\TestCase;

class DeleteBookTest extends TestCase
{
    public function testDeleteBookReturnsTrueWhenBookExists()
    {
        $repo = Mockery::mock(BookRepository::class);
        $bookId = 10;

        // Arrange: existing book entity
        $book = Book::create(
            title: 'Test Book',
            author: 'Test Author',
            isbn: '1234567890',
            description: 'Some description',
            genres: ['fantasy'],
            stock: 5,
        );
        $book->setId($bookId);

        $repo->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn($book);

        $repo->shouldReceive('delete')
            ->once()
            ->with($bookId)
            ->andReturn(true);

        $useCase = new DeleteBook($repo);
        $result = $useCase->execute($bookId);

        $this->assertTrue($result);
    }

    public function testDeleteBookReturnsNullWhenBookNotFound()
    {
        $repo = Mockery::mock(BookRepository::class);
        $bookId = 999;

        $repo->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn(null);

        $repo->shouldReceive('delete')->never();

        $useCase = new DeleteBook($repo);
        $result = $useCase->execute($bookId);

        $this->assertNull($result);
    }
}
