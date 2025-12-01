<?php

namespace App\Application\BookRental\DTO;

use App\Application\Book\DTO\BookDTO;
use App\Application\User\DTO\UserDTO;
use App\Domain\BookRental\Entities\BookRental;

class BookRentalDTO
{
    public function __construct(
        public int $id,
        public BookDTO $book,
        public UserDTO $user,
        public string $startDate,
        public string $dueDate,
        public float $progressPercent,
    ) {}

    public static function fromEntity(
        BookRental $rental,
        BookDTO $books,
        UserDTO $users
    ): self {

        return new self(
            id: $rental->getId(),
            book: $books,
            user: $users,
            startDate: $rental->getStartDate()->format('Y-m-d H:i:s'),
            dueDate: $rental->getDueDate()->format('Y-m-d H:i:s'),
            progressPercent: $rental->getProgressPercent(),
        );
    }
    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'book'            => $this->book->toArray(),
            'user'            => $this->user->toArray(),
            'start_date'      => $this->startDate,
            'due_date'        => $this->dueDate,
            'progress_percent'=> $this->progressPercent,
        ];
    }

}
