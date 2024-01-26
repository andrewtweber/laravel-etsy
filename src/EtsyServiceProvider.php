<?php

namespace Etsy;

use Etsy\Models\Shop;
use Etsy\Models\ShopItem;
use Etsy\Observers\ShopItemObserver;
use Etsy\Observers\ShopObserver;
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
        /** @phpstan-ignore-next-line */
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('etsy'); /** @phpstan-ignore-line */
        }

        $this->mergeConfigFrom($config, 'etsy');

        $this->registerObservers();
    }

    protected function registerObservers()
    {
        // TODO: use config models here?
        Shop::observe(ShopObserver::class);
        ShopItem::observe(ShopItemObserver::class);
    }
}
