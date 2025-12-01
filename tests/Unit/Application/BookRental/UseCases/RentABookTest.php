<?php

namespace Tests\Unit\Application\BookRental\UseCases;

use App\Application\BookRental\DTO\BookRentalDTO;
use App\Application\BookRental\UseCases\RentABook;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Exceptions\BookNotAvailableException;
use App\Domain\Book\Repositories\BookRepository;
use App\Domain\BookRental\Entities\BookRental;
use App\Domain\BookRental\Repositories\BookRentalRepository;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class RentABookTest extends TestCase
{
    public function testRentABookSuccess(){
        $bookRepository   = Mockery::mock(BookRepository::class);
        $userRepository   = Mockery::mock(UserRepository::class);
        $rentalRepository = Mockery::mock(BookRentalRepository::class);

        $bookId = 1;
        $userId = 2;

        $user = User::register(
            name: 'Test User',
            email: 'test@example.com',
            passwordHash: 'hashed-password',
        );

        $user->setId($userId);

        $book = Book::create(
            title: 'Test Book',
            author: 'Author',
            isbn: '1234567890',
            description: null,
            genres: ['fantasy'],
            stock: 3,
        );
        $book->setId($bookId);

        $userRepository->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturn($user);

        $bookRepository->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn($book);

        $bookRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(fn (Book $bookEntity) => $bookEntity);

        $rentalRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function (BookRental $rental) {
                $rental->setId(1);   // simulate database auto-increment
                return $rental;
            });

        $useCase = new RentABook($rentalRepository,$bookRepository, $userRepository);
        $result = $useCase->execute($userId, $bookId);
        $this->assertInstanceOf(BookRentalDTO::class, $result);
        $this->assertSame(2, $book->getStock());

        $this->assertSame($userId, $result->user->id);
        $this->assertSame($bookId, $result->book->id);

    }

    public function testRentABookThrowsWhenBookNotAvailable(){
        $bookRepository   = Mockery::mock(BookRepository::class);
        $userRepository   = Mockery::mock(UserRepository::class);
        $rentalRepository = Mockery::mock(BookRentalRepository::class);

        $bookId = 1;
        $userId = 2;

        $book = Book::create(
            title: 'Test Book',
            author: 'Author',
            isbn: '1234567890',
            description: null,
            genres: ['fantasy'],
            stock: 0,
        );
        $book->setId($bookId);

        $bookRepository->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn($book);

        $this->expectException(BookNotAvailableException::class);
        $this->expectExceptionMessage('Book is not available.');

        $useCase = new RentABook($rentalRepository,$bookRepository, $userRepository);
        $useCase->execute($userId, $bookId);

    }

    public function testRentABookNotFoundBook(){
        $bookRepository   = Mockery::mock(BookRepository::class);
        $userRepository   = Mockery::mock(UserRepository::class);
        $rentalRepository = Mockery::mock(BookRentalRepository::class);

        $bookId = 999;
        $userId = 2;

        $bookRepository->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Book is not found.');

        $useCase = new RentABook($rentalRepository,$bookRepository, $userRepository);
        $useCase->execute($userId, $bookId);

    }
}
