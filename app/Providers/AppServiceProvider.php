<?php

namespace App\Providers;

use App\Domain\Repositories\PackageRepositoryInterface;
use App\Infrastructure\Auth\JwtAuthService;
use App\Infrastructure\Persistence\Repositories\EloquentPackageRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PackageRepositoryInterface::class, EloquentPackageRepository::class);

        $this->app->singleton(JwtAuthService::class, function ($app) {
            return new JwtAuthService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
