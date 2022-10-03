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
