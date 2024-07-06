# Honeybadger Laravel integration

[![Latest Version on Packagist](https://img.shields.io/packagist/v/honeybadger-io/honeybadger-laravel.svg?style=flat-square)](https://packagist.org/packages/honeybadger-io/honeybadger-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/honeybadger-io/honeybadger-laravel.svg?style=flat-square)](https://packagist.org/packages/honeybadger-io/honeybadger-laravel)
![Run Tests](https://github.com/honeybadger-io/honeybadger-laravel/workflows/Run%20Tests/badge.svg?branch=master)
[![StyleCI](https://styleci.io/repos/138627377/shield)](https://github.styleci.io/repos/138627377)

This is the Laravel library for integrating apps with the :zap: [Honeybadger Exception Notifier for Laravel](https://www.honeybadger.io/for/laravel/?utm_source=github&utm_medium=readme&utm_campaign=laravel&utm_content=Honeybadger+Exception+Notifier+for+Laravel).

## Documentation and Support

For comprehensive documentation and support, check out our [documentation site](https://docs.honeybadger.io/lib/php/index.html):

- [Laravel Integration Guide](https://docs.honeybadger.io/lib/php/integration/laravel.html)
- [Lumen Integration Guide](https://docs.honeybadger.io/lib/php/integration/lumen.html)

## Testing
``` bash
> composer test
```

## Code Style
This project follows the [PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md). In addition, StyleCI will apply the [Laravel preset](https://docs.styleci.io/presets#laravel).

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Releasing
We have enabled GitHub integration with [Packagist](https://packagist.org). Packagist is automatically notified when a new release is made on GitHub.

Releases are automated, using [Github Actions](https://github.com/honeybadger-io/honeybadger-laravel/blob/master/.github/workflows/release.yml):
- When a PR is merged on master, the [run-tests.yml](https://github.com/honeybadger-io/honeybadger-laravel/blob/master/.github/workflows/run-tests.yml) workflow is executed, which runs the tests.
- If the tests pass, the [release.yml](https://github.com/honeybadger-io/honeybadger-laravel/blob/master/.github/workflows/release.yml) workflow will be executed.
- Depending on the commit message, a release PR will be created with the suggested the version bump and changelog. Note: Not all commit messages trigger a new release, for example, chore: ... will not trigger a release.
- If the release PR is merged, the release.yml workflow will be executed again, and this time it will create a github release.

## Credits
- [TJ Miller](https://github.com/sixlive)
- [All Contributors](../../contributors)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
