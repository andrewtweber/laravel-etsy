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
        $config = realpath(__DIR__ . '/../config/etsy.php');

        if ($this->app instanceof LaravelApplication) {
            $this->publishes([
                $migrations => database_path('migrations'),
                $config     => config_path('etsy.php'),
            ]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('etsy');
        }

        $this->mergeConfigFrom($config, 'etsy');
    }
}
