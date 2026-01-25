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
            'title' => $this->fake()->words($this->fake()->numberBetween(2, 5), true),
            'author' => $this->fake()->firstName(),
            'description' => $this->fake()->paragraph(1),
            'genres' => $this->fake()->randomElements(['Fiction', 'Horror', 'Drama', 'Fantasy', 'Comedy', 'History'], $this->fake()->numberBetween(1, 3)),
            'stock' => $this->fake()->numberBetween(0, 10),
            'isbn'=> $this->fake()->unique()->md5()
        ];
    }
}
