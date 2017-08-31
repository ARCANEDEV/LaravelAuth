# 1. Installation

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)
  
## Server Requirements

The LaravelAuth package has a few system requirements:

```
- PHP >= 7.0
```

## Version Compatibility

| LaravelAuth                           | Laravel                                                                                |
|:--------------------------------------|:---------------------------------------------------------------------------------------|
| ![LaravelAuth v0.x][laravel_auth_0_x] | ![Laravel v5.1][laravel_5_1] ![Laravel v5.2][laravel_5_2] ![Laravel v5.3][laravel_5_3] |
| ![LaravelAuth v1.x][laravel_auth_1_x] | ![Laravel v5.4][laravel_5_4]                                                           |
| ![LaravelAuth v2.x][laravel_auth_2_x] | ![Laravel v5.5][laravel_5_5]                                                           |

[laravel_5_1]:    https://img.shields.io/badge/v5.1-supported-brightgreen.svg?style=flat-square "Laravel v5.1"
[laravel_5_2]:    https://img.shields.io/badge/v5.2-supported-brightgreen.svg?style=flat-square "Laravel v5.2"
[laravel_5_3]:    https://img.shields.io/badge/v5.3-supported-brightgreen.svg?style=flat-square "Laravel v5.3"
[laravel_5_4]:    https://img.shields.io/badge/v5.4-supported-brightgreen.svg?style=flat-square "Laravel v5.4"
[laravel_5_5]:    https://img.shields.io/badge/v5.5-supported-brightgreen.svg?style=flat-square "Laravel v5.5"

[laravel_auth_0_x]: https://img.shields.io/badge/version-0.*-blue.svg?style=flat-square "LaravelAuth v0.*"
[laravel_auth_1_x]: https://img.shields.io/badge/version-1.*-blue.svg?style=flat-square "LaravelAuth v1.*"
[laravel_auth_2_x]: https://img.shields.io/badge/version-2.*-blue.svg?style=flat-square "LaravelAuth v2.*"

## Composer

You can install this package via [Composer](http://getcomposer.org/) by running this command: `composer require arcanedev/laravel-auth`.

## Setup

> **NOTE :** The package will automatically register itself if you're using Laravel `>= v5.5`, so you can skip this section.

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
