<?php

namespace App\Infrastructure\Presistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

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

    public function rentals(): HasMany
    {
        return $this->hasMany(BookRentalModel::class, 'book_id');
    }

    public function scopeHasActiveRental(Builder $query): Builder
    {
        return $query->whereHas('rentals', function (Builder $q) {
            $q->where('progress','<', 100);
        });
    }
}
