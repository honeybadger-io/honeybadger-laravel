# Honeybadger Laravel integration

[![Latest Version on Packagist](https://img.shields.io/packagist/v/honeybadger-io/honeybadger-laravel.svg?style=flat-square)](https://packagist.org/packages/honeybadger-io/honeybadger-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/honeybadger-io/honeybadger-laravel.svg?style=flat-square)](https://packagist.org/packages/honeybadger-io/honeybadger-laravel)
[![Build Status](https://img.shields.io/travis/honeybadger-io/honeybadger-laravel/master.svg?style=flat-square)](https://travis-ci.org/honeybadger-io/honeybadger-laravel)
[![Quality Score](https://img.shields.io/scrutinizer/g/honeybadger-io/honeybadger-laravel.svg?style=flat-square)](https://scrutinizer-ci.com/g/honeybadger-io/honeybadger-laravel)
[![Maintainability](https://api.codeclimate.com/v1/badges/8fdf4e1917297a9921d4/maintainability)](https://codeclimate.com/github/honeybadger-io/honeybadger-laravel/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/8fdf4e1917297a9921d4/test_coverage)](https://codeclimate.com/github/honeybadger-io/honeybadger-laravel/test_coverage)
[![StyleCI](https://styleci.io/repos/138627377/shield)](https://github.styleci.io/repos/138627377)

This is the Laravel library for integrating apps with the :zap: [Honeybadger Exception Notifier for PHP](http://honeybadger.io).

## Installation
You can install the package via composer:

```bash
> composer require honeybadger-io/honeybadger-laravel
```

## Laravel

This package uses Laravel's [package discovery](https://laravel.com/docs/5.6/packages#package-discovery) to register the service provider and facade to the framework. If you are using an older version of Laravel or do not use package discovery see below.

### Register the provider with the Framework

`config/app.php`  

```php
'providers' => [
    /*
    * Package Service Providers...
    */
    \Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider::class,
]
```

### Register the facade with the framework

`config/app.php`  

```php
'aliases' => [
    'Honeybadger' => \Honeybadger\HoneybadgerLaravel\Facades\Honeybadger::class,
]
```

### Publish the configuration
You can publish the config file with:
```bash
> php artisan vendor:publish --provider="Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider" --tag="config"
```

This will add the [configuration](#configuration) file to `config/honeybadger.php`.

## Lumen
### Register the provider with the Framework
Add the following line to `bootstrap/app.php` under the "Register Service Providers" section.

```php
$app->register(\Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider::class);
```

### Publish the configuration
Manually add the [configuration](#configuration) file to `config/honeybadger.php`.

## Configuration

_For more details on how this configuration is used, please reference the PHP library documentation at [honeybadger-io/honeybadger-php](https://github.com/honeybadger-io/honeybadger-php)._

```php
return [
    'api_key' => env('HONEYBADGER_API_KEY'),
    'environment' => [
        'filter' => [],
        'include' => [],
    ],
    'request' => [
        'filter' => [],
    ],
    // 'version' => trim(exec('git log --pretty="%h" -n1 HEAD')),
    'version' => env('APP_VERSION'),
    'hostname' => gethostname(),
    'project_root' => base_path(),
    'environment_name' => env('APP_ENV'),
    'handlers' => [
        'exception' => true,
        'error' => true,
    ],
    'client' => [
        'timeout' => 0,
        'proxy' => [],
    ],
    'excluded_exceptions' => [],
];

```

## Usage
Add the honeybadger notifier to the application exception handler's report method.

`app/Exceptions/Handler.php`  

```php
public function report(Exception $exception)
{
    if (app()->bound('honeybadger') && $this->shouldReport($exception)) {
        app('honeybadger')->notify($exception, app('request'));
    }

    parent::report($exception);
}
```

### Adding Context
Context can be added by either the provided Facade or by resolving from the service container.

**Facade**
```php
Honeybadger::context('key', $value);
```

**DI Resolution**
```php
use Honeybadger\Honeybadger;

public function __construct(Honeybadger $honeybadger)
{
    $honeybadger->context('key', $value);
}
```

**Helper Resolution**
```php
use Honeybadger\Honeybadger;

public function __construct()
{
    app('honeybadger')->context('key', $value);
    app(Honeybadger::class)->context('key', $value)
}
```

### Adding Default User Context
The provided middleware will add the users auth identifier context automatically. ***This middleware should be applied after your any middleware that handles authentication***.

#### Laravel
`app/Http/Kernel.php`  

```php
protected $middlewareGroup = [
    'web' => [
        \Honeybadger\HoneybadgerLaravel\Middleware\UserContext::class,
    ]
];
```

#### Lumen
`bootstrap/app.php`  
```php
 $app->middleware([
     \Honeybadger\HoneybadgerLaravel\Middleware\UserContext::class
 ]);
```

### Test Honeybadger Connection
This will send a test exception to the HB application for you to verify everything is working correctly.

```bash
> php artisan honeybadger:test
```

### Honeybadger Check-In
The library allows for easy integration with [Honeybadger's Check-In feature](https://www.honeybadger.io/check-ins).

#### Scheduled Command
This method is great for ensuring your application is up and running.

`app/Console/Kernel.php`  

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('honeybadger:checkin 1234')->everyFiveMinutes();
}
```

#### After a Scheduled Command
This method is great for making sure specific scheduled commands are running on time.

`app/Console/Kernel.php`  

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('inspire')
        ->everyFiveMinutes()
        ->thenPingHoneybadger('1234');
}
```

#### After a Scheduled Command
`app/Console/Kernel.php`  

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('inspire')
        ->everyFiveMinutes()
        ->thenPingHoneybadger('1234');
}
```

#### Command
```bash
> php artisan honeybadger:checkin 1234
```

## Testing
``` bash
> composer test
```

## Code Style
In addition to the php-cs-fixer rules, StyleCI will apply the [Laravel preset](https://docs.styleci.io/presets#laravel).
```bash
> composer styles:lint
> composer styles:fix
```

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits
- [TJ Miller](https://github.com/sixlive)
- [All Contributors](../../contributors)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
