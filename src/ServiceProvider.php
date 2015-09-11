<?php

namespace CipeMotion\Medialibrary;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'medialibrary');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'medialibrary');

        $this->publishes([
            __DIR__ . '/../resources/config/medialibrary.php' => config_path('medialibrary.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/medialibrary.php', 'medialibrary');
    }
}
