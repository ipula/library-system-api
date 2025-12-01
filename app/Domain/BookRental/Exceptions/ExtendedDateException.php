<?php

namespace App\Domain\BookRental\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class ExtendedDateException extends DomainException
{
    public function __construct(string $message = 'New due date must be later than current due date.')
    {
        parent::__construct($message);
    }
    public function getStatusCode(): int
    {
       return 422;
    }
}
