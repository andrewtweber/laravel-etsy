<?php

namespace Etsy;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * Class EtsyServiceProvider
 *
 * @package Etsy
 */
class EtsyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $migrations = realpath(__DIR__ . '/../database/migrations');

        if ($this->app instanceof LaravelApplication) {
            $this->publishes([
                $migrations => database_path('migrations'),
            ]);
        } elseif ($this->app instanceof LumenApplication) {
            //
        }
    }
}
