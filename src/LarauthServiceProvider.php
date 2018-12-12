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
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Bahraminekoo\Larauth\Controllers\AdminBaseController');
    }
}
