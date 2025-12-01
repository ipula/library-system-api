<?php

namespace App\Domain\Book\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class InsufficientStockException extends DomainException
{
    public function __construct(string $message = 'Not enough stock.')
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 422;
    }
}
