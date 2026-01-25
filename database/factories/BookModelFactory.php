<?php

namespace Database\Factories;

use App\Infrastructure\Presistence\Eloquent\Models\BookModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookModelFactory extends Factory
{
    protected $model = BookModel::class;
    public function definition()
    {
        return [
            'title' => fake()->words(fake()->numberBetween(2, 5), true),
            'author' => fake()->firstName(),
            'description' => fake()->paragraph(1),
            'genres' => fake()->randomElements(['Fiction', 'Horror', 'Drama', 'Fantasy', 'Comedy', 'History'], fake()->numberBetween(1, 3)),
            'stock' => fake()->numberBetween(0, 10),
            'isbn'=> fake()->unique()->md5()
        ];
    }
}
