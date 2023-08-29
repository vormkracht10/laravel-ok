# Is your Laravel app OK?

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vormkracht10/laravel-ok.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-ok)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/vormkracht10/laravel-ok/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/vormkracht10/laravel-ok/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/vormkracht10/laravel-ok/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/vormkracht10/laravel-ok/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/vormkracht10/laravel-ok.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/laravel-ok)

Health checks made in production to ensure you can sleep well at night and be sure everything is still OK.

## Installation

You can install the package via composer:

```bash
composer require vormkracht10/laravel-ok
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-ok-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-ok-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php

```

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
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
