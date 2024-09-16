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

You can now run the checks using the `ok:check` Artisan command:

```bash
php artisan ok:check
```

## Available checks

- **Cache Check**: Check whether reading and writing to the cache is possible.
- **Composer Outdated Check**: Checks whether there are outdated packages in your project and informs you of the findings.
- **Composer Audit Check**: Checks whether there are any security vulnerabilities in your composer dependencies.
- **CPU Load Check**: Checks whether the total CPU load is above a certain percentage on a short, mid and long term.
- **Config Cache Check**: Checks whether the config is cached.
- **Database Check**: Checks whether a connection can be established on the configured connection.
- **Database Connection Count Check**: Checks whether the database's connection count exceeds a configurable limit.
- **Database Size Check**: Checks whether the database's data exceeds a configurable size limit.
- **Database Table Size Check**: Does the same as the Database Size Check but for a specific table.
- **Debug Mode Check**: Checks whether debug mode is enabled.
- **Directory Check**: Checks whether the configured directories exist.
- **Disk Space Check**: Checks whether the disk space is below a certain threshold.
- **DotEnv Check**: Checks whether the configured environment variables are accessible in the application.
- **Environment Check**: Checks whether the current environment matches the configured environment type.
- **Event Cache Check**: Checks whether events are cached.
- **Extension Check**: Checks whether the configured PHP extensions are loaded.
- **File Check**: Does the same as the Directory Check but for files.
- **Horizon Check**: Checks whether Horizon is running.
- **Memory Usage Check**: Checks whether the total memory usage exceeds a configurable limit in the form of a percentage.
- **NPM Audit Check**: Checks whether there are any security vulnerabilities in your npm dependencies.
- **NPM Installed Package Check**: Checks whether a certain npm package is installed.
- **OPCache Check**: Checks whether OP cache and optionally the JIT compiler are configured and running.
- **Permission Check**: Checks whether the configured filesystem items have the correct permissions for the current user.
- **Ping check**: Checks whether the application can ping the specified address, if the address is not specified it defaults to `www.google.com`.
- **Process Count Check**: Checks whether the configured programs are exceeding the configured process count.
- **Queue Check**: Checks whether the queue is running.
- **Reboot Check**: Checks whether the server has rebooted recently.
- **Redis Check**: Checks whether the application can connect to the configured redis connections.
- **Redis Memory Usage Check**: Checks whether the Redis instance is exceeding a configured amount of memory usage.
- **Route Cache Check**: Checks whether routes are cached.
- **Scheduler Check**: Checks whether the scheduler has is still online and running jobs.
- **Storage Check**: Checks whether the configured disks can be written to and read from.
- **UptimeCheck**: Checks whether the server's uptime exceeds a configured maximum.

## Inspired by

This package is inspired by [Laravel Health](https://github.com/spatie/laravel-health).

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Mark van Eijk](https://github.com/markvaneijk)
-   [Bas van Dinther](https://github.com/baspa)
-   [David den Haan](https://github.com/daviddenhaan)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
- 
