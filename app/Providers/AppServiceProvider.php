<?php

namespace App\Providers;

use App\Models\Tournament;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->routeBindings();
    }

    protected function routeBindings(): void
    {
        Route::model('tournamentId', Tournament::class);
    }
}
