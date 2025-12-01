<?php

namespace App\Infrastructure\Providers;

use App\Domain\Book\Repositories\BookRepository;
use App\Domain\BookRental\Repositories\BookRentalRepository;
use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\Presistence\Eloquent\Repositories\EloquentBookRentalRepository;
use App\Infrastructure\Presistence\Eloquent\Repositories\EloquentBookRepository;
use App\Infrastructure\Presistence\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(
            UserRepository::class,
            EloquentUserRepository::class
        );
        $this->app->bind(
            BookRepository::class,
            EloquentBookRepository::class
        );
        $this->app->bind(
            BookRentalRepository::class,
            EloquentBookRentalRepository::class
        );
    }
}
