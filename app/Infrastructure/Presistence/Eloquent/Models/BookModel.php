<?php

namespace App\Infrastructure\Presistence\Eloquent\Models;

use Database\Factories\BookModelFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class BookModel extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return BookModelFactory::new();
    }

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
        'genres' => 'array',
    ];

    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtolower($value),
            get: fn ($value) => ucfirst($value)
        );
    }
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
