<?php

namespace Database\Factories;

use App\Infrastructure\Presistence\Eloquent\Models\BookModel;
use App\Infrastructure\Presistence\Eloquent\Models\BookRentalModel;
use App\Infrastructure\Presistence\Eloquent\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookRentalModelFactory extends Factory
{
    protected $model = BookRentalModel::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-2 weeks', 'now');
        $due = (clone $start)->modify('+2 weeks');

        return [
            'user_id' => UserModel::factory(),
            'book_id' => BookModel::factory(),
            'start_date' => $start,
            'due_date'   => $due,
            'end_date'   => null,
            'progress' => fake()->numberBetween(0, 90),
        ];
    }

    public function finished(): static
    {
        return $this->state(function () {
            return [
                'end_date' => fake()->dateTimeBetween('-1 week', 'now'),
                'progress' => 100,
            ];
        });
    }
}
