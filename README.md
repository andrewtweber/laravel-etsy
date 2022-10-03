# Laravel Etsy

## Installation

```
composer require andrewtweber/laravel-etsy
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

## Console Commands

Add the commands to your console Kernel.

```php
protected $commands = [
    \Etsy\Console\Commands\EtsyTaxonomies::class,
    \Etsy\Console\Commands\EtsyUpdateListings::class,
];

protected function schedule(Schedule $schedule)
{
    $schedule->command('etsy:shops')->dailyAt('06:00');

    $schedule->command('email:welcome')->dailyAt('11:00');
}
```

## Extending

If you need to extend any of the Etsy classes, you can do so and update the configuration
to point to the new model.

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

## Recommendations

* Make sure to add some policies for the `Shop` and `ShopItem` classes. 
* Some sample Nova models are supplied, copy them into your `app/Nova` folder. 
