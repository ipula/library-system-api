<?php

namespace App\Domain\BookRental\Entities;

use App\Domain\BookRental\Exceptions\BookRentalProgressException;
use App\Domain\BookRental\Exceptions\ExtendedDateException;

class BookRental
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private int $bookId,
        private \DateTimeImmutable $startDate,
        private \DateTimeImmutable $dueDate,
        private ?\DateTimeImmutable $endDate = null,
        private float $progressPercent = 0, // 0 → 100
    ) {}

    public static function create(
        int $userId,
        int $bookId,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $dueDate,
        ?\DateTimeImmutable $endDate = null,
        ?float $progressPercent = 0
    ): self
    {
        return new self(
            id: null,
            userId: $userId,
            bookId: $bookId,
            startDate: $startDate,
            dueDate: $dueDate,
            endDate: $endDate,
            progressPercent: $progressPercent,
        );
    }
    public function extend(\DateTimeImmutable $newDueDate): void
    {
        if ($newDueDate <= $this->dueDate) {
            throw new ExtendedDateException();
        }
        $this->dueDate = $newDueDate;
    }

    public function updateProgress(float $percent): void
    {
        if ($percent < 0 || $percent > 100) {
            throw new BookRentalProgressException();
        }
        if ($percent < $this->progressPercent) {
            return;
        }
        $this->progressPercent = $percent;
    }

    public function finish(): void
    {
        $this->endDate = new \DateTimeImmutable();
        $this->progressPercent = 100;
    }

    public function isFinished(): bool
    {
        return $this->endDate !== null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getDueDate(): \DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getProgressPercent(): float
    {
        return $this->progressPercent;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setBookId(int $bookId): void
    {
        $this->bookId = $bookId;
    }

    public function setStartDate(\DateTimeImmutable $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function setDueDate(\DateTimeImmutable $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function setProgressPercent(int $progressPercent): void
    {
        if ($progressPercent < 0 || $progressPercent > 100) {
            throw new \DomainException("Progress must be between 0–100.");
        }

        $this->progressPercent = $progressPercent;
    }

}
