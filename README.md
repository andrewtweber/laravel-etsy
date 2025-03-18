[![Laravel Etsy](https://raw.githubusercontent.com/andrewtweber/laravel-etsy/master/art/banner.png)](https://andrew.cool)

# Laravel Etsy

[![CircleCI](https://dl.circleci.com/status-badge/img/gh/andrewtweber/laravel-etsy/tree/master.svg?style=shield)](https://dl.circleci.com/status-badge/redirect/gh/andrewtweber/laravel-etsy/tree/master)

## Installation

(This is not actually published yet, as it is in pre-alpha. I do not follow semantic versioning, so use at your own risk)


```
composer require andrewtweber/laravel-etsy
```

To publish the config and migrations, run:

```
php artisan vendor:publish --provider="Etsy\EtsyServiceProvider" 
```

## User Model

Add the `EtsyUserInterface` interface and `EtsyUser` trait to your `User` class.

```php
use Etsy\EtsyUser;
use Etsy\EtsyUserInterface;

class User extends Model implements EtsyUserInterface
{
    use EtsyUser;
}
```

## API

Go to your [Etsy developer account](https://www.etsy.com/developers/your-apps) to get your API keys and
add them to your `.env`

```
ETSY_API_KEY=
ETSY_API_SECRET=
```

## Console Commands

Add the commands to your console Kernel.

```php
protected $commands = [
    \Etsy\Console\Commands\EtsyTaxonomies::class,
    \Etsy\Console\Commands\EtsyUpdateListings::class,
];

protected function schedule(Schedule $schedule)
{
    // Recommend running this daily to sync shops and items
    $schedule->command('etsy:shops')->dailyAt('06:00');
}

### For Laravel 12: 

To register the commands, add to the `boot` method within `AppServiceProvider`

```
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Artisan::starting(function ($artisan) {
            $artisan->resolveCommands([
                \Etsy\Console\Commands\EtsyTaxonomies::class,
                \Etsy\Console\Commands\EtsyUpdateListings::class,
            ]);
        });
    }
    ```


```

## Taxonomy

This command probably only needs to be run once, or very rarely:

```
php artisan etsy:taxonomy
```

This will fetch all of Etsy's taxonomies which can then be mapped to your own custom categories.

## Extending Models

If you need to extend any of the Etsy models or pivots, you can do so and update the configuration
to point to your model class.

Sample model:

```php
namespace App\Models;

class Shop extends \Etsy\Models\Shop implements HasCommentsInterface
{
    use HasComments;
}
```

Config:

```php
return [
    'models' => [
        'shop' => \App\Models\Shop::class,
    ],
];
```

## Events

`ShopItemPhotoFetched` - when a new item is synced, this event gets dispatched which contains the
shop item, the full URL to the image, and Etsy's external ID for that image. You can process this
(save it to storage, etc) by listening for this event.

## Recommendations

* Make sure to add some policies for the `Shop` and `ShopItem` classes.
* Some sample Nova models are supplied, copy them into your `app/Nova` folder.

## TODO

* Not sure if the observers can be registered in the package service provider, especially if the model classes are
  overridden.
