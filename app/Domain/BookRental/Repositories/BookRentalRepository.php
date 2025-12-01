<?php

namespace App\Domain\BookRental\Repositories;

use App\Domain\BookRental\Entities\BookRental;

interface BookRentalRepository
{
    public function save(BookRental $rental): BookRental;

    public function findRentalById(int $rentalId): ?BookRental;
}
