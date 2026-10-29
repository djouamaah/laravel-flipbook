<?php

namespace Djouamaah\Flipbook;

use Illuminate\Support\ServiceProvider;

class FlipbookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/flipbook.php', 'flipbook');
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'flipbook');

        if (config('flipbook.load_migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/flipbook.php' => config_path('flipbook.php'),
            ], 'flipbook-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/flipbook'),
            ], 'flipbook-views');

            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'flipbook-migrations');
        }
    }
}
