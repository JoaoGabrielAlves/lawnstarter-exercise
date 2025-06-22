<?php

namespace App\Providers;

use App\Repositories\Contracts\StarWarsRepositoryContract;
use App\Repositories\StarWarsRepository;
use App\Services\Contracts\StarWarsServiceContract;
use App\Services\StarWarsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository contracts to implementations
        $this->app->bind(StarWarsRepositoryContract::class, StarWarsRepository::class);

        // Bind Service contracts to implementations
        $this->app->bind(StarWarsServiceContract::class, StarWarsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
