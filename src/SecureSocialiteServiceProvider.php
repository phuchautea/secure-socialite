<?php


namespace SecureSocialite;

use Illuminate\Support\ServiceProvider;

class SecureSocialiteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../config/secure-socialite.php' => config_path('secure-socialite.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/secure-socialite.php',
            'secure-socialite'
        );
    }
}
