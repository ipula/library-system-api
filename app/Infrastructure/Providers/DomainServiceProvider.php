<?php

namespace App\Infrastructure\Providers;

use App\Domain\Book\Repositories\BookRepository;
use App\Domain\User\Repositories\UserRepository;
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
    }
}
