<?php

namespace App\Domain\User\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class UserHasActiveRentalsException extends DomainException
{
    public function __construct(string $message = 'User has an active book rentals')
    {
        parent::__construct($message);
    }
    public function getStatusCode(): int
    {
        return 422;
    }
}
