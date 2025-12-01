<?php

namespace Tests\Unit\Application\BookRental\UseCases;

use App\Application\BookRental\UseCases\UpdateProgress;
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

class UpdateProgressTest extends TestCase
{
    public function testUpdateProgressSuccess()
    {
        $bookRentalRepo = Mockery::mock(BookRentalRepository::class);
        $bookRepo       = Mockery::mock(BookRepository::class);
        $userRepo       = Mockery::mock(UserRepository::class);

        $rentalId = 10;
        $userId   = 1;
        $bookId   = 5;
        $progress = 60.0;

        $rental = BookRental::create(
            userId: $userId,
            bookId: $bookId,
            startDate: new \DateTimeImmutable('2024-01-01'),
            dueDate: new \DateTimeImmutable('2024-01-15'),
        );
        $rental->setId($rentalId);

        // User and Book for DTO mapping
        $user = User::register(
            name: 'John Doe',
            email: 'john@example.com',
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

        $bookRentalRepo->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn($rental);

        $bookRentalRepo->shouldReceive('save')
            ->once()
            ->with($rental)
            ->andReturnUsing(function (BookRental $bookRental) use ($progress) {
                $bookRental->setProgressPercent($progress);
                return $bookRental;
            });

        $userRepo->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturn($user);

        $bookRepo->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn($book);

        $useCase = new UpdateProgress($bookRentalRepo, $bookRepo, $userRepo);
        $dto = $useCase->execute($rentalId, $progress);

        $this->assertNotNull($dto);
        $this->assertSame($rentalId, $dto->id);
        $this->assertSame($userId, $dto->user->id);
        $this->assertSame($bookId, $dto->book->id);
        $this->assertSame($progress, $dto->progressPercent);
    }

    public function testUpdateProgressThrowsWhenRentalNotFound()
    {
        $bookRentalRepo = Mockery::mock(BookRentalRepository::class);
        $bookRepo       = Mockery::mock(BookRepository::class);
        $userRepo       = Mockery::mock(UserRepository::class);

        $rentalId = 999;
        $progress = 50.0;

        $bookRentalRepo->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn(null);

        $bookRentalRepo->shouldReceive('save')->never();
        $bookRepo->shouldReceive('findById')->never();
        $userRepo->shouldReceive('findById')->never();

        $useCase = new UpdateProgress($bookRentalRepo, $bookRepo, $userRepo);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('No book rental found.');

        $useCase->execute($rentalId, $progress);
    }

    public function testUpdateProgressThrowsWhenRentalAlreadyFinished()
    {
        $bookRentalRepo = Mockery::mock(BookRentalRepository::class);
        $bookRepo       = Mockery::mock(BookRepository::class);
        $userRepo       = Mockery::mock(UserRepository::class);

        $rentalId = 10;
        $userId   = 1;
        $bookId   = 5;
        $progress = 80.0;

        $rental = BookRental::create(
            userId: $userId,
            bookId: $bookId,
            startDate: new \DateTimeImmutable('2024-01-01'),
            dueDate: new \DateTimeImmutable('2024-01-15'),
        );
        $rental->setId($rentalId);
        $rental->finish();

        $bookRentalRepo->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn($rental);

        $bookRentalRepo->shouldReceive('save')->never();
        $bookRepo->shouldReceive('findById')->never();
        $userRepo->shouldReceive('findById')->never();

        $useCase = new UpdateProgress($bookRentalRepo, $bookRepo, $userRepo);

        $this->expectException(RentalAlreadyFinishedException::class);

        $useCase->execute($rentalId, $progress);
    }
}
