<?php

namespace App\Domain\Book\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class InvalidStockAmountException extends DomainException
{
    public function __construct(string $message = 'Decrease amount must be positive.')
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 422;
    }
}
