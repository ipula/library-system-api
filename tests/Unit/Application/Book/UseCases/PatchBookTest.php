<?php

namespace Tests\Unit\Application\Book\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\DTO\PatchBookDTO;
use App\Application\Book\UseCases\PatchBook;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use Mockery;
use Tests\TestCase;

class PatchBookTest extends TestCase
{
    public function testPatchBookUpdatesFieldsAndReturnsDto()
    {
        $repo = Mockery::mock(BookRepository::class);

        $bookId = 10;

        $book = Book::create(
            title: 'Old Title',
            author: 'Old Author',
            isbn: '1111111111',
            description: 'Desc',
            genres: ['old'],
            stock: 5,
        );
        $book->setId($bookId);

        $patchData = [
            'title'  => 'New Title',
            'author' => 'New Author',
            'stock'  => 10,
            // leave isbn & genres untouched
        ];

        $patchDto = new PatchBookDTO(
            id: $bookId,
            data: $patchData,
        );

        $repo->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn($book);

        $repo->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (Book $b) use ($patchData) {
                // Check changed fields
                $ok =
                    $b->getTitle() === $patchData['title'] &&
                    $b->getAuthor() === $patchData['author'] &&
                    $b->getStock() === $patchData['stock'];

                // Check unchanged fields (isbn, description, genres)
                $ok = $ok &&
                    $b->getIsbn() === '1111111111' &&
                    $b->getDescription() === 'Desc' &&
                    $b->getGenres() === ['old'];

                return $ok;
            }))
            ->andReturnUsing(fn (Book $b) => $b);

        $useCase = new PatchBook($repo);

        // Act
        $result = $useCase->execute($patchDto);

        // Assert
        $this->assertInstanceOf(BookDTO::class, $result);

        if (method_exists($result, 'toArray')) {
            $data = $result->toArray();
            $this->assertSame('New Title', $data['title']);
            $this->assertSame('New Author', $data['author']);
            $this->assertSame(10, $data['stock']);
            $this->assertSame('1111111111', $data['isbn']);
        }
    }

    public function testPatchBookReturnsNullWhenBookNotFound()
    {
        $repo = Mockery::mock(BookRepository::class);

        $bookId = 999;
        $patchDto = new PatchBookDTO(
            id: $bookId,
            data: ['title' => 'Whatever'],
        );

        $repo->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn(null);

        $repo->shouldReceive('save')->never();

        $useCase = new PatchBook($repo);

        $result = $useCase->execute($patchDto);

        $this->assertNull($result);
    }
}
