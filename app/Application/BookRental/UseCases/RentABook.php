<?php

namespace App\Application\BookRental\UseCases;

use App\Application\Book\DTO\BookDTO;
use App\Application\BookRental\DTO\BookRentalDTO;
use App\Application\User\DTO\UserDTO;
use App\Domain\Book\Exceptions\BookNotAvailableException;
use App\Domain\Book\Repositories\BookRepository;
use App\Domain\BookRental\Entities\BookRental;
use App\Domain\BookRental\Repositories\BookRentalRepository;
use App\Domain\User\Repositories\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RentABook
{
    public function __construct(
        private BookRentalRepository $bookRentalRepository,
        private BookRepository $bookRepository,
        private UserRepository $userRepository
    ) {}

    public function execute(int $userId,int $bookId): ?BookRentalDTO
    {
        $book = $this->bookRepository->findById($bookId);
        if(!$book){
            throw new NotFoundHttpException('Book is not found.');
        }
        if(!$book->isAvailable()){
            throw new BookNotAvailableException();
        }

        #Decrease stock and save book
        $book->decreaseStock($userId);
        $this->bookRepository->save($book);

        $start = new \DateTimeImmutable();
        $dueDate   = $start->modify('+2 weeks');

        $rental = BookRental::create(
            userId: 4,
            bookId: $bookId,
            startDate: $start,
            dueDate: $dueDate,
        );
        // Create the rental entity and persist it
        $rental = $this->bookRentalRepository->save($rental);

        $user= $this->userRepository->findById(4);
        $bookDto = BookDTO::fromEntity($book);
        $userDto = UserDTO::fromEntity($user);

        // Return DTO to controller
        return BookRentalDTO::fromEntity($rental, $bookDto, $userDto);
    }
}
