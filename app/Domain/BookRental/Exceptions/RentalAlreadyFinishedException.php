<?php

namespace App\Domain\BookRental\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class RentalAlreadyFinishedException extends DomainException
{
    public function __construct(string $message = 'This rental is already finished.')
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 409;
    }
}
