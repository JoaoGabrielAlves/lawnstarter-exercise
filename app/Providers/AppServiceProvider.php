<?php

namespace App\Providers;

use App\Repositories\Contracts\StarWarsRepositoryContract;
use App\Repositories\Contracts\StatisticsRepositoryContract;
use App\Repositories\StarWarsRepository;
use App\Repositories\StatisticsRepository;
use App\Services\Contracts\RequestTrackingServiceContract;
use App\Services\Contracts\StarWarsServiceContract;
use App\Services\Contracts\StatisticsServiceContract;
use App\Services\RequestTrackingService;
use App\Services\StarWarsService;
use App\Services\StatisticsService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Star Wars API bindings
        $this->app->bind(StarWarsRepositoryContract::class, StarWarsRepository::class);
        $this->app->bind(StarWarsServiceContract::class, StarWarsService::class);

        // Statistics bindings
        $this->app->bind(StatisticsRepositoryContract::class, StatisticsRepository::class);
        $this->app->bind(StatisticsServiceContract::class, StatisticsService::class);

        // Request tracking bindings
        $this->app->bind(RequestTrackingServiceContract::class, RequestTrackingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', static function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
