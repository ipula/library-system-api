<?php

namespace App\Domain\BookRental\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class BookRentalProgressException extends DomainException
{
    public function __construct(string $message = 'Progress must be between 0 and 100.')
    {
        parent::__construct($message);
    }
    public function getStatusCode(): int
    {
        return 422;
    }
}
