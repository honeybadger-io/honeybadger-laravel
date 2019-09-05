# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Fixed
- Error whent here is a component and not an action

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
