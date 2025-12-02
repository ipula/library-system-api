<?php

namespace Database\Seeders;

use App\Infrastructure\Presistence\Eloquent\Models\BookModel;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run()
    {
        BookModel::factory(20)->create();
    }
}
