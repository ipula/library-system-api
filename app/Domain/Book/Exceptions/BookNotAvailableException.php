<?php

namespace App\Domain\Book\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class BookNotAvailableException extends DomainException
{

    public function __construct(string $message = 'Book is not available.')
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 422;
    }
}
