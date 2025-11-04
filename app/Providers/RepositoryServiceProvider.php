<?php

namespace App\Providers;

use App\Repositories\Contracts\DislikeRepositoryInterface;
use App\Repositories\Contracts\LikeRepositoryInterface;
use App\Repositories\Contracts\PhotoRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\DislikeRepository;
use App\Repositories\LikeRepository;
use App\Repositories\PhotoRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            LikeRepositoryInterface::class,
            LikeRepository::class
        );

        $this->app->bind(
            DislikeRepositoryInterface::class,
            DislikeRepository::class
        );

        $this->app->bind(
            PhotoRepositoryInterface::class,
            PhotoRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function bootstrap(): void
    {
        //
    }
}
