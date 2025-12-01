<?php

namespace Tests\Unit\Application\BookRental\UseCases;

use App\Application\BookRental\UseCases\RentalReadingProgress;
use App\Domain\BookRental\Entities\BookRental;
use App\Domain\BookRental\Repositories\BookRentalRepository;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class RentalReadingProgressTest extends TestCase
{
    public function testReturnsProgressWhenRentalExists()
    {
        $repository = Mockery::mock(BookRentalRepository::class);

        $rentalId = 10;

        // Fake rental entity (adapt constructor/factory to your real one)
        $rental = BookRental::create(
            userId: 1,
            bookId: 5,
            startDate: new \DateTimeImmutable('2024-01-01'),
            dueDate: new \DateTimeImmutable('2024-01-15'),
        );
        $rental->setId($rentalId);
        $rental->setProgressPercent(42); // assume you have this setter

        $repository->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn($rental);

        $useCase = new RentalReadingProgress($repository);

        $progress = $useCase->execute($rentalId);

        $this->assertSame(42, $progress);
    }

    public function testThrowsNotFoundWhenRentalMissing()
    {
        $repository = Mockery::mock(BookRentalRepository::class);

        $rentalId = 999;

        $repository->shouldReceive('findRentalById')
            ->once()
            ->with($rentalId)
            ->andReturn(null);

        $useCase = new RentalReadingProgress($repository);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Rental not found.');

        $useCase->execute($rentalId);
    }
}
