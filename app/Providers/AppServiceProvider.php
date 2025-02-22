<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Yajra\DataTables\DataTablesServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register any application services here
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the DataTables service provider manually (optional)
        $this->app->register(DataTablesServiceProvider::class);
    }
}

