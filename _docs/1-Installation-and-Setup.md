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
| ![LaravelAuth v3.x][laravel_auth_3_x] | ![Laravel v5.4][laravel_5_4]                                                           |
| ![LaravelAuth v4.x][laravel_auth_4_x] | ![Laravel v5.5][laravel_5_5]                                                           |
| ![LaravelAuth v5.x][laravel_auth_5_x] | ![Laravel v5.6][laravel_5_6]                                                           |
| ![LaravelAuth v6.x][laravel_auth_6_x] | ![Laravel v5.7][laravel_5_7]                                                           |

[laravel_5_1]:    https://img.shields.io/badge/v5.1-supported-brightgreen.svg?style=flat-square "Laravel v5.1"
[laravel_5_2]:    https://img.shields.io/badge/v5.2-supported-brightgreen.svg?style=flat-square "Laravel v5.2"
[laravel_5_3]:    https://img.shields.io/badge/v5.3-supported-brightgreen.svg?style=flat-square "Laravel v5.3"
[laravel_5_4]:    https://img.shields.io/badge/v5.4-supported-brightgreen.svg?style=flat-square "Laravel v5.4"
[laravel_5_5]:    https://img.shields.io/badge/v5.5-supported-brightgreen.svg?style=flat-square "Laravel v5.5"
[laravel_5_6]:    https://img.shields.io/badge/v5.6-supported-brightgreen.svg?style=flat-square "Laravel v5.6"
[laravel_5_7]:    https://img.shields.io/badge/v5.7-supported-brightgreen.svg?style=flat-square "Laravel v5.7"

[laravel_auth_0_x]: https://img.shields.io/badge/version-0.*-blue.svg?style=flat-square "LaravelAuth v0.*"
[laravel_auth_3_x]: https://img.shields.io/badge/version-3.*-blue.svg?style=flat-square "LaravelAuth v3.*"
[laravel_auth_4_x]: https://img.shields.io/badge/version-4.*-blue.svg?style=flat-square "LaravelAuth v4.*"
[laravel_auth_5_x]: https://img.shields.io/badge/version-5.*-blue.svg?style=flat-square "LaravelAuth v5.*"
[laravel_auth_6_x]: https://img.shields.io/badge/version-6.*-blue.svg?style=flat-square "LaravelAuth v6.*"

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
