# Changelog

All notable changes to this project will be documented in this file.

## [1.2.3] - 2026-06-18

### Fixed

- Fixed a bug in the Twig helper functions `isActiveRoute()` and `currentRoute()`
- Fixed wrong release date in CHANGELOG.md for release `1.2.1`

## [1.2.2] - 2026-06-18

### Added

- Few `justify-content-*` CSS helpers

## [1.2.1] - 2026-06-17

### Fixed
Fixed some code issues in Flash class and related methods/functions
Fixed wrong `route()` Twig code example in README.md

### Changed
Url encode route parameter values by default in `App\Routing\Router::route()`

## [v1.2.0] - 2026-05-14

### Added

- New command `make:dto`
- New CSS classes for a hierarchy ordered list

### Fixed
- Fixed code in `RegisterDto.php`
- Fixed frontend error handling for `auth_errors` in `login.twig`

## [1.1.0] - 2026-05-14

### Added

- New commands:
  - `make:controller`
  - `make:middleware`
  - `make:provider`
  - `make:model`
- Added new helper function `app_path()`

### Fixed

- Added missing setup script for setting correct permissions on storage directories during `composer create-setup ...` execution.

### Changed

- Unified command naming
- Moved system commands into subdirectory `app/Console/Commands/Beacon`.

## [1.0.0] - 2026-05-15
- Initial public release