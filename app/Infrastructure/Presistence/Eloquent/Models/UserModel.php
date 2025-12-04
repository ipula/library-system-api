<?php

namespace App\Infrastructure\Presistence\Eloquent\Models;

use Database\Factories\UserModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class UserModel extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;
    protected static function newFactory()
    {
        return UserModelFactory::new();
    }
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(BookRentalModel::class, 'user_id');
    }

    public function scopeHasActiveRental(Builder $query): Builder
    {
        return $query->whereHas('rentals', function (Builder $q) {
            $q->where('progress', '<',100);
        });
    }

}
