<?php

namespace App\Application\BookRental\UseCases;

use App\Domain\BookRental\Repositories\BookRentalRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RentalReadingProgress
{

    public function __construct(
        private BookRentalRepository $rentalRepository,
    ) {}

    public function execute(int $rentalId): int
    {
        $rental = $this->rentalRepository->findRentalById($rentalId);

        if (!$rental) {
            throw new NotFoundHttpException("Rental not found.");
        }

        return $rental->getProgressPercent();
    }
}
