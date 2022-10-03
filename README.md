# Laravel Etsy

## Installation

```
composer require andrewtweber/laravel-etsy
```

Add the `EtsyUserInterface` interface and `EtsyUser` trait to your `User` class.

```php
use Etsy\EtsyUser;
use Etsy\EtsyUserInterface;

class User extends Model implements EtsyUserInterface
{
    use EtsyUser;
}
```
