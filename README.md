# Is your Laravel app OK?

[![Total Downloads](https://img.shields.io/packagist/dt/vormkracht10/laravel-ok.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-ok)
[![Tests](https://github.com/vormkracht10/laravel-ok/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/vormkracht10/laravel-ok/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/vormkracht10/laravel-ok/actions/workflows/phpstan.yml/badge.svg?branch=main)](https://github.com/vormkracht10/laravel-ok/actions/workflows/phpstan.yml)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/vormkracht10/laravel-ok)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/vormkracht10/laravel-ok)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/vormkracht10/laravel-ok.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-ok)

Health checks made in production to ensure you can sleep well at night and be sure everything is still OK.

## Installation

You can install the package via composer:

```bash
composer require vormkracht10/laravel-ok
```

You can then install the package by using the `ok:install` Artisan command:

```bash
php artisan ok:install
```

This is the contents of the published config file:

```php
return [
    'notifications' => [
        'enabled' => env('LARAVEL_OK_NOTIFICATIONS_ENABLED', true),

        'failed_notification' => CheckFailedNotification::class,

        'notifiable' => Notifiable::class,

        'via' => [
            // 'discord' => [
            //     'channel' => 123456790,
            // ],
            // 'mail' => [
            //     'to' => 'text@example.com',
            // ],
            // 'slack' => [
            //     'webhook' => 'webhook-url',
            // ],
            // 'telegram' => [
            //     'channel' => 1234567890,
            // ],
        ],
    ],

    'checks' => [
        'audit' => [
            'path' => [
                // '~/some/bin',
            ],
        ],
    ],
];
```

## Usage

To register checks for your application, you need to register them in the `checks` array in your `AppServiceProvider` register method.

```php

use Vormkracht10\LaravelOK\Facades\OK;

class AppServiceProvider extends ServiceProvider
{
    // ...

    public function register()
    {
        OK::checks([
            EnvironmentCheck::shouldBe('production'),
            DebugModeCheck::shouldBe('false'),
        ]);
    }
}
```

## Available checks

✅ **Cache Check**: Check if reading and writing to the cache is possible.

✅ **Composer Audit Check**: Checks if there are any security vulnerabilities in your composer dependencies.

✅ **Config Cache Check**: Checks if the configured cache matches the given value.

✅ **Debug Mode Check**: Checks if debug mode is enabled.

✅ **Disk Space Check**: Checks if the disk space is below a certain threshold.

✅ **Environment Check**: Checks if the current environment matches the given environment.

✅ **Event Cache Check**: Checks if events are cached.

✅ **Horizon Check**: Checks if Horizon is running.

✅ **Npm Audit Check**: Checks if there are any security vulnerabilities in your npm dependencies.

✅ **Queue Check**: Checks if the queue is running.

✅ **Route Cache Check**: Checks if routes are cached.


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Mark van Eijk](https://github.com/markvaneijk)
-   [Bas van Dinther](https://github.com/baspa)
-   [David den Haan](https://github.com/dulkoss)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
