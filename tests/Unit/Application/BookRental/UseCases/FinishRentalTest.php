<?php

namespace Tests\Unit\Application\BookRental\UseCases;

use App\Application\BookRental\DTO\BookRentalDTO;
use App\Application\BookRental\UseCases\FinishRental;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use App\Domain\BookRental\Entities\BookRental;
use App\Domain\BookRental\Exceptions\RentalAlreadyFinishedException;
use App\Domain\BookRental\Repositories\BookRentalRepository;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class FinishRentalTest extends TestCase
{
    public function testFinishRentalRentalNotFound(){
        $bookRepository   = Mockery::mock(BookRepository::class);
        $userRepository   = Mockery::mock(UserRepository::class);
        $rentalRepository = Mockery::mock(BookRentalRepository::class);

        $rentalId = 1;

        $rentalRepository->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('No book rental found.');

        $useCase = new FinishRental($rentalRepository,$bookRepository, $userRepository);
        $useCase->execute($rentalId);
    }

    public function testFinishRentalRentalAlreadyFinished(){
        $bookRepository   = Mockery::mock(BookRepository::class);
        $userRepository   = Mockery::mock(UserRepository::class);
        $rentalRepository = Mockery::mock(BookRentalRepository::class);

        $bookId = 1;
        $userId = 2;
        $rentalId = 1;

        $rental = BookRental::create(
            userId: $userId,
            bookId: $bookId,
            startDate: new \DateTimeImmutable(),
            dueDate: (new \DateTimeImmutable())->modify('+2 weeks'),
        );
        $rental->setId($rentalId);
        $rental->setEndDate(new \DateTimeImmutable());

        $rentalRepository->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn($rental);

        $this->expectException(RentalAlreadyFinishedException::class);
        $this->expectExceptionMessage('This rental is already finished.');

        $useCase = new FinishRental($rentalRepository,$bookRepository, $userRepository);
        $useCase->execute($rentalId);
    }

    public function testFinishRentalSuccess(){

        $bookRepository   = Mockery::mock(BookRepository::class);
        $userRepository   = Mockery::mock(UserRepository::class);
        $rentalRepository = Mockery::mock(BookRentalRepository::class);

        $bookId = 1;
        $userId = 2;
        $rentalId = 1;

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

        $rental = BookRental::create(
            userId: $userId,
            bookId: $bookId,
            startDate: new \DateTimeImmutable(),
            dueDate: (new \DateTimeImmutable())->modify('+2 weeks'),
        );
        $rental->setId($rentalId);

        $userRepository->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturn($user);

        $bookRepository->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn($book);

        $rentalRepository
            ->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn($rental);

        $bookRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(fn (Book $bookEntity) => $bookEntity);

        $rentalRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function (BookRental $bookRental) {
                $bookRental->setId(1);
                return $bookRental;
            });

        $useCase = new FinishRental($rentalRepository,$bookRepository, $userRepository);
        $result = $useCase->execute($rental->getId());
        $this->assertInstanceOf(BookRentalDTO::class, $result);

        $this->assertSame(4, $book->getStock());
        $this->assertSame(100.0, $rental->getProgressPercent());

        $this->assertSame($userId, $result->user->id);
        $this->assertSame($bookId, $result->book->id);
    }
}
