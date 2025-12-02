<?php

namespace App\Infrastructure\Presistence\Eloquent\Repositories;

use App\Domain\BookRental\Entities\BookRental;
use App\Domain\BookRental\Repositories\BookRentalRepository;
use App\Infrastructure\Presistence\Eloquent\Models\BookRentalModel;

class EloquentBookRentalRepository implements BookRentalRepository
{
    public function __construct(
        private BookRentalModel $model
    ) {}
    public function save(BookRental $rental): BookRental
    {
        if ($rental->getId() === null) {
            $model = new $this->model();
        } else {
            $model = $this->model->newQuery()->findOrFail($rental->getId());
        }

        $model->user_id    = $rental->getUserId();
        $model->book_id    = $rental->getBookId();
        $model->start_date = $rental->getStartDate()->format('Y-m-d H:i:s');
        $model->due_date   = $rental->getDueDate()->format('Y-m-d H:i:s');
        $model->end_date   = $rental->getEndDate()?->format('Y-m-d H:i:s');
        $model->progress   = $rental->getProgressPercent();

        $model->save();

        // Convert back into domain entity
        return $this->toEntity($model);
    }
    public function findRentalById(int $rentalId): ?BookRental
    {
        $model = $this->model->newQuery()->find($rentalId);
        if (!$model) {
            return null;
        }
        return $this->toEntity($model);
    }

    private function toEntity($model): BookRental
    {
        return new BookRental(
            id: $model->id,
            userId: $model->user_id,
            bookId: $model->book_id,
            startDate: new \DateTimeImmutable($model->start_date),
            dueDate: new \DateTimeImmutable($model->due_date),
            endDate: $model->end_date
                ? new \DateTimeImmutable($model->end_date)
                : null,
            progressPercent: (float)$model->progress,
        );
    }
}
