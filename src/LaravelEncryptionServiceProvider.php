<?php

namespace Canaryau\LaravelEncryption;

use Illuminate\Support\ServiceProvider;

class LaravelEncryptionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-encryption.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-encryption');

        $this->app->singleton('laravel_encryption', function () {
            return new LaravelEncryption();
        });
    }
}
