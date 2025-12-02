<?php

namespace App\Infrastructure\Presistence\Eloquent\Models;

use Database\Factories\BookModelFactory;
use Database\Factories\BookRentalModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRentalModel extends Model
{
    use HasFactory;
    protected $table = 'book_rentals';

    protected static function newFactory()
    {
        return BookRentalModelFactory::new();
    }
}
