# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.14.0] - 2022-05-06
### Added
- Call `shouldReport()` of parent ([#97](https://github.com/honeybadger-io/honeybadger-php/pull/97))
- Add `capture_deprecations` config flag ([#97](https://github.com/honeybadger-io/honeybadger-php/pull/97))

## [3.13.1] - 2022-02-16
### Added
- Support Dotenv editor v2 ([#96](https://github.com/honeybadger-io/honeybadger-php/pull/96))

## [3.13.0] - 2022-01-22
### Added
- Support for Laravel 9 ([#93](https://github.com/honeybadger-io/honeybadger-php/pull/93))

### Fixed
- Set HONEYBADGER_VERIFY_SSL default in .env during installation ([#94](https://github.com/honeybadger-io/honeybadger-php/pull/94))

## [3.12.1] - 2021-11-22
### Fixed
- Install command: don't error when there's no base route ([#90](https://github.com/honeybadger-io/honeybadger-php/pull/90))

## [3.12.0] - 2021-07-02
### Added
- Added custom log driver ([#17](https://github.com/honeybadger-io/honeybadger-laravel/pull/17))

## [3.11.0] - 2021-06-16
- Set default client timeout ([#87](https://github.com/honeybadger-io/honeybadger-laravel/pull/87))

## [3.10.0] - 2021-05-09
### Added
- Added `report_data` to default config ([#85](https://github.com/honeybadger-io/honeybadger-laravel/pull/85))

## [3.9.0] - 2021-04-12
### Added
- Honeybadger can now set route action and user context automatically—no middleware needed ([#82](https://github.com/honeybadger-io/honeybadger-laravel/pull/82/))

## [3.8.1] - 2021-03-25
### Fixed
- Silence any thrown errors in breadcrumbs collection ([#81](https://github.com/honeybadger-io/honeybadger-laravel/pull/81))

## [3.8.0] - 2021-03-23
### Added
- Automatic breadcrumbs ([#79](https://github.com/honeybadger-io/honeybadger-laravel/pull/79))

## [3.7.0] - 2021-03-09
### Added
- Added ability to enable job pings on specific environments ([#73](https://github.com/honeybadger-io/honeybadger-laravel/pull/73))
- Include the PHP SDK version in error reports ([#75](https://github.com/honeybadger-io/honeybadger-laravel/pull/75))
- Added the `@honeybadgerError` and `@honeybadgerFeedback` Blade directives ([#76](https://github.com/honeybadger-io/honeybadger-laravel/pull/76))

## [3.6.0] - 2021-02-19
### Added
- Log ServiceExceptions rather than crash ([#70](https://github.com/honeybadger-io/honeybadger-laravel/pull/70))

## [3.5.0] - 2021-02-15
### Added
- Verify SSL config ([#68](https://github.com/honeybadger-io/honeybadger-laravel/pull/68))

## [3.4.0] - 2020-12-17
### Changed
- Allowed some install failures ([#66])(https://github.com/honeybadger-io/honeybadger-laravel/pull/66)

## [3.3.0] - 2020-11-29
### Added
- Support for PHP8 ([#64](https://github.com/honeybadger-io/honeybadger-laravel/pull/64))
-
## [3.2.0] - 2020-09-14
### Added
- Support for Laravel 8 ([#59](https://github.com/honeybadger-io/honeybadger-laravel/pull/59))

## [3.1.0] - 2020-03-23
### Changed
- Bumped [`honeybadger-io/honeybadger-php`](https://github.com/honeybadger-io/honeybadger-php) to `^2.1` from `^2.0`

## [3.0.0] - 2020-03-09
### Breaking Changes
- Dropped support for Laravel 6 and added support for Laravel 7 ([#54](https://github.com/honeybadger-io/honeybadger-laravel/pull/54))


## [2.1.0] - 2019-12-05
### Added
- Added `pingHoneybadgerOnSuccess` method to scheduled tasks ([#49](https://github.com/honeybadger-io/honeybadger-laravel/pull/49))

## [2.0.1] - 2019-10-01
### Fixed
- Route action and component for Lumen ([#42](https://github.com/honeybadger-io/honeybadger-laravel/pull/42))

## [2.0.0] - 2019-09-23
### Changed
- Support for Laravel 6 ([#41](https://github.com/honeybadger-io/honeybadger-laravel/pull/41))

## [1.7.3] - 2019-09-23
### Changed
- Drops Laravel 6 support, need new major version ([#40](https://github.com/honeybadger-io/honeybadger-laravel/pull/40))

## [1.7.2] - 2019-09-05
### Fixed
- Error whent here is a component and not an action ([#37](https://github.com/honeybadger-io/honeybadger-laravel/pull/37))

## [1.7.1] - 2019-09-05
### Fixed
* Request action and component ([#35](https://github.com/honeybadger-io/honeybadger-laravel/pull/35))

## [1.7.0] - 2019-09-04
### Added
* Controller action and component to middleware ([#31](https://github.com/honeybadger-io/honeybadger-laravel/pull/31))

### Changed
* Updated support for Laravel 6.0 ([#32](https://github.com/honeybadger-io/honeybadger-laravel/pull/32))
* Increased the minimum version of the honeybadger-php package ([#33](https://github.com/honeybadger-io/honeybadger-laravel/pull/33))

## [1.6.0] - 2019-06-27
### Added
* Deploy command ([#26](https://github.com/honeybadger-io/honeybadger-laravel/pull/26))

## [1.5.0] - 2018-12-17
### Added
* PHP 7.3 to Travis ([#19](https://github.com/honeybadger-io/honeybadger-laravel/pull/19))

### Removed
* php-cs-fixer dev dependency ([#20](https://github.com/honeybadger-io/honeybadger-laravel/pull/20))

## [1.4.0] - 2018-11-02
### Added
* Installer command for PHP & Laravel ([#11](https://github.com/honeybadger-io/honeybadger-laravel/pull/11))

### Changed
* Updated version contraints ([#15](https://github.com/honeybadger-io/honeybadger-laravel/pull/15))

## [1.3.0] - 2018-09-13
### Added
* Support for Laravel 5.7 ([#13](https://github.com/honeybadger-io/honeybadger-laravel/pull/13))

### Changed
* Updated the Travis CI config ([#14](https://github.com/honeybadger-io/honeybadger-laravel/pull/14))
* Updated [honeybadger-io/honeybadger-php](https://github.com/honeybadger-io/honeybadger-php) ([#14](https://github.com/honeybadger-io/honeybadger-laravel/pull/14))

## [1.2.0] - 2018-08-17
### Changed
* Updated [honeybadger-io/honeybadger-php](https://github.com/honeybadger-io/honeybadger-php) to `^1.1` ([#10](https://github.com/honeybadger-io/honeybadger-laravel/pull/10))

## [1.1.0] - 2018-08-08
### Added
* Scheduled event helper to ping honeybadger after task is complete (#5)

### Changed
* Changes exception type reported by the test command for more clarity in the HB application UI. (#4)

## [1.0.0] - 2018-07-10
* Initial release
