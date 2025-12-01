<?php

namespace App\Application\BookRental\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\BookRental\DTO\BookRentalDTO;
use App\Application\User\DTO\UserDTO;
use App\Domain\Book\Repositories\BookRepository;
use App\Domain\BookRental\Repositories\BookRentalRepository;
use App\Domain\User\Repositories\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExtendRental
{
    public function __construct(
        private BookRentalRepository $bookRentalRepository,
        private BookRepository $bookRepository,
        private UserRepository $userRepository
    ) {}

    public function execute(int $id,\DateTimeImmutable $extendedDate): ?BookRentalDTO
    {
        $rental = $this->bookRentalRepository->findRentalById($id);
        if(!$rental){
            throw new NotFoundHttpException('No book rental found.');
        }
        $rental->extend($extendedDate);
        // update the rental entity
        $updatedRental = $this->bookRentalRepository->save($rental);
        $user = $this->userRepository->findById($rental->getUserId());
        $book = $this->bookRepository->findById($rental->getBookId());
        $bookDto = BookDTO::fromEntity($book);
        $userDto = UserDTO::fromEntity($user);
        // Return DTO to controller
        return BookRentalDTO::fromEntity($updatedRental, $bookDto,$userDto);
    }
}
