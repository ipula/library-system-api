<?php

namespace App\Domain\BookRental\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class BookHasActiveRentalsExceptionextends extends DomainException
{
    public function __construct(
        string $message = 'Book has active rentals and cannot be deleted.'
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 422;
    }
}
