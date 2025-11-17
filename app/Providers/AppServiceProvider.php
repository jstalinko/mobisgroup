<?php

namespace App\Providers;

use App\Services\DramaboxService;
use App\Services\NetshortService;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\MovieServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $service = "dramabox";

        $this->app->bind(MovieServiceInterface::class, function () use ($service) {
            return match ($service) {
                'netshort' => new NetshortService(),
                default => new DramaboxService(),
            };
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
