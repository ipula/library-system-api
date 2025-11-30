<?php

namespace App\Infrastructure\Presistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookModel extends Model
{
    use SoftDeletes;
    protected $table = 'books';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'genres',
        'stock'
    ];

    protected $casts = [
        'genres' => 'array',   //  THIS automatically json_decodes()
    ];
}
