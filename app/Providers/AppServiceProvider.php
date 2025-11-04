<?php

namespace App\Providers;

use App\Strategies\RandomRecommendationStrategy;
use App\Strategies\RecommendationStrategyInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind recommendation strategy
        $this->app->bind(
            RecommendationStrategyInterface::class,
            RandomRecommendationStrategy::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listeners
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\UserReached50LikesEvent::class,
            [\App\Listeners\SendAdminNotificationListener::class, 'handle']
        );
    }
}
