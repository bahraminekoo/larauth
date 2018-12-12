<?php
namespace Bahraminekoo\Larauth;

use Illuminate\Support\ServiceProvider;

class LarauthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/routes/routes.php';
        }
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom( __DIR__ . '/views', 'larauth');

        $this->publishes([
            __DIR__.'/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/larauth'),
        ], 'views');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Bahraminekoo\Larauth\Controllers\SignUpController');
    }
}
