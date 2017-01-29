# 2. Installation

## Composer

You can install this package via [Composer](http://getcomposer.org/) by running this command: `composer require arcanedev/laravel-auth`.

Or by adding the package to your `composer.json`.

```json
{
    "require": {
        "arcanedev/laravel-auth": "~0.11"
    }
}
```

Then install it via `composer install` or `composer update`.

## Setup

Once the package is installed, you can register the service provider in `config/app.php` in the `providers` array:

```php
// config/app.php

'providers' => [
    ...
    Arcanedev\LaravelAuth\LaravelAuthServiceProvider::class,
],
```

### Artisan commands

Publish the package config file, migrations and model factories to your application by running this command :

```bash
$ php artisan vendor:publish --provider="Arcanedev\LaravelAuth\LaravelAuthServiceProvider"
```

You can also separate the publish command into three commands by adding the `--tag` flag :

```bash
$ php artisan vendor:publish --provider="Arcanedev\LaravelAuth\LaravelAuthServiceProvider" --tag=config
```

```bash
$ php artisan vendor:publish --provider="Arcanedev\LaravelAuth\LaravelAuthServiceProvider" --tag=migrations
```

```bash
$ php artisan vendor:publish --provider="Arcanedev\LaravelAuth\LaravelAuthServiceProvider" --tag=factories
```

And to force the publish command to override the files, you just add the `--force`.
