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
            'title' => $this->faker->words($this->faker->numberBetween(2, 5), true),
            'author' => $this->faker->firstName(),
            'description' => $this->faker->paragraph(1),
            'genres' => $this->faker->randomElements(['Fiction', 'Horror', 'Drama', 'Fantasy', 'Comedy', 'History'], $this->faker->numberBetween(1, 3)),
            'stock' => $this->faker->numberBetween(0, 10),
            'isbn'=> $this->faker->unique()->md5()
        ];
    }
}
