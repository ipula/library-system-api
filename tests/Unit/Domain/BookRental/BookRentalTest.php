<?php

namespace Tests\Unit\Domain\BookRental;

use App\Domain\BookRental\Entities\BookRental;
use App\Domain\BookRental\Exceptions\BookRentalProgressException;
use App\Domain\BookRental\Exceptions\ExtendedDateException;
use Tests\TestCase;

class BookRentalTest extends TestCase
{
    public function testBookRentalExtendDateNotGreaterThanDueDate(): void
    {
        $this->expectException(ExtendedDateException::class);
        $this->expectExceptionMessage('New due date must be later than current due date.');

        $bookRental = BookRental::create(
            userId: 1,
            bookId: 1,
            startDate: new \DateTimeImmutable(),
            dueDate: (new \DateTimeImmutable())->modify('+2 weeks'),
        );

        $bookRental->extend(new \DateTimeImmutable());
    }

    public function testBookRentalUpdateProgressInvalidProgress(): void
    {
        $this->expectException(BookRentalProgressException::class);
        $this->expectExceptionMessage('Progress must be between 0 and 100.');

        $bookRental = BookRental::create(
            userId: 1,
            bookId: 1,
            startDate: new \DateTimeImmutable(),
            dueDate: (new \DateTimeImmutable())->modify('+2 weeks'),
        );

        $bookRental->updateProgress(200);
    }

    public function testBookRentalFinishReturnFalseForNotFinishRental(): void
    {
        $bookRental = BookRental::create(
            userId: 1,
            bookId: 1,
            startDate: new \DateTimeImmutable(),
            dueDate: (new \DateTimeImmutable())->modify('+2 weeks'),
        );

        $this->assertFalse($bookRental->isFinished());
    }

    public function testBookRentalFinishReturnTureForFinishRental(): void
    {
        $bookRental = BookRental::create(
            userId: 1,
            bookId: 1,
            startDate: new \DateTimeImmutable(),
            dueDate: (new \DateTimeImmutable())->modify('+2 weeks'),
        );
        $bookRental->finish();
        $this->assertTrue($bookRental->isFinished());
    }

}
