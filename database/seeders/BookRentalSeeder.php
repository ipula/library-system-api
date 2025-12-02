<?php

namespace Database\Seeders;

use App\Infrastructure\Presistence\Eloquent\Models\BookModel;
use App\Infrastructure\Presistence\Eloquent\Models\BookRentalModel;
use Illuminate\Database\Seeder;

class BookRentalSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure we have books and users first
        if (BookModel::count() === 0) {
            $this->call(BookSeeder::class);
        }

        // Create some rentals
        BookRentalModel::factory()->count(10)->create();

        // Also create some finished rentals
        BookRentalModel::factory()
            ->count(10)
            ->finished()
            ->create();
    }
}
