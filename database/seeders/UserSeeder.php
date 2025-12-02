<?php

namespace Database\Seeders;

use App\Infrastructure\Presistence\Eloquent\Models\UserModel;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        UserModel::factory(10)->create();
    }
}
