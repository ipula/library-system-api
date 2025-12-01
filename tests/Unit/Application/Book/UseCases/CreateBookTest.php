<?php

namespace Tests\Unit\Application\Book\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\DTO\CreateBookInput;
use App\Application\Book\UseCases\CreateBook;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use Mockery;
use Tests\TestCase;

class CreateBookTest extends TestCase
{
    public function testCreateBookSuccess()
    {
        $repo = Mockery::mock(BookRepository::class);

        // Arrange input DTO (adjust property names if needed)
        $input = new CreateBookInput(
            title: 'Test Book',
            author: 'Test Author',
            genre: ['fantasy', 'adventure'],
            isbn: '1234567890',
            description: 'Test description',
            stock: 10,
        );

        $repo->shouldReceive('save')
            ->once()
            ->andReturnUsing(function (Book $book) use ($input){
                $book->setId(1);
                $book->setTitle($input->title);
                $book->setAuthor($input->author);
                $book->setGenre($input->genre);
                $book->setIsbn($input->isbn);
                $book->setDescription($input->description);
                $book->setStock($input->stock);
                return $book;
            });

        $useCase = new CreateBook($repo);
        $dto = $useCase->execute($input);
        $this->assertInstanceOf(BookDTO::class, $dto);

        $data = $dto->toArray();

        $this->assertSame('Test Book', $data['title']);
        $this->assertSame('Test Author', $data['author']);
        $this->assertSame('1234567890', $data['isbn']);
        $this->assertSame('Test description', $data['description']);
        $this->assertSame(['fantasy', 'adventure'], $data['genres'] ?? $data['genre'] ?? null);
        $this->assertSame(10, $data['stock']);
    }
}
