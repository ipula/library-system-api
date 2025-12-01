<?php

namespace Tests\Unit\Application\BookRental\UseCases;

use App\Application\BookRental\DTO\BookRentalDTO;
use App\Application\BookRental\UseCases\ExtendRental;
use App\Application\BookRental\UseCases\RentABook;
use App\Domain\Book\Entities\Book;
use App\Domain\Book\Repositories\BookRepository;
use App\Domain\BookRental\Entities\BookRental;
use App\Domain\BookRental\Exceptions\ExtendedDateException;
use App\Domain\BookRental\Repositories\BookRentalRepository;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepository;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ExtendRentalTest extends TestCase
{
    public function testExtendRentalSuccess(){
        $bookRepository   = Mockery::mock(BookRepository::class);
        $userRepository   = Mockery::mock(UserRepository::class);
        $rentalRepository = Mockery::mock(BookRentalRepository::class);

        $bookId = 1;
        $userId = 2;
        $rentalId = 1;
        $extendedDate = (new \DateTimeImmutable())->modify('+4 weeks');

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

        $rentalRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function (BookRental $bookRental) {
                $bookRental->setId(1);
                return $bookRental;
            });

        $useCase = new ExtendRental($rentalRepository,$bookRepository, $userRepository);
        $result = $useCase->execute($rental->getId(),$extendedDate);
        $this->assertInstanceOf(BookRentalDTO::class, $result);

        $this->assertSame($userId, $result->user->id);
        $this->assertSame($bookId, $result->book->id);

    }

    public function testRentalRecordNotFound(){
        $bookRepository   = Mockery::mock(BookRepository::class);
        $userRepository   = Mockery::mock(UserRepository::class);
        $rentalRepository = Mockery::mock(BookRentalRepository::class);

        $rentalId = 1;
        $extendedDate = (new \DateTimeImmutable())->modify('+4 weeks');

        $rentalRepository->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('No book rental found.');

        $useCase = new ExtendRental($rentalRepository,$bookRepository, $userRepository);
        $useCase->execute($rentalId, $extendedDate);

    }

    public function testExtendRentalDueDateGreaterThanNewExtendDate(){
        $bookRepository   = Mockery::mock(BookRepository::class);
        $userRepository   = Mockery::mock(UserRepository::class);
        $rentalRepository = Mockery::mock(BookRentalRepository::class);

        $bookId = 1;
        $userId = 2;
        $rentalId = 1;
        $extendedDate = (new \DateTimeImmutable())->modify('+4 weeks');

        $rental = BookRental::create(
            userId: $userId,
            bookId: $bookId,
            startDate: new \DateTimeImmutable(),
            dueDate: (new \DateTimeImmutable())->modify('+8 weeks'),
        );

        $rentalRepository->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn($rental);

        $this->expectException(ExtendedDateException::class);
        $this->expectExceptionMessage('New due date must be later than current due date.');

        $useCase = new ExtendRental($rentalRepository,$bookRepository, $userRepository);
        $useCase->execute($rentalId, $extendedDate);

    }
}
