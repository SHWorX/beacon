# Changelog

All notable changes to this project will be documented in this file.

## [1.3.0]

### Changed
- Updated composer.json to use the latest minor versions of dependencies

## [1.3.0]

### Added
- New command InstallCommand (not finished yet)
- New command helpers: ask(), confirm(), choice()
- New CSS styles for dropdown navigation element incl. sub menu
- New HTTP 429 error
- Dedicated (internal) API routes
- JavaScript API client for usage in templates
- User account settings page to change email and password
- Correct "autocomplete" usages to forms
- Missing CSS declarations
- Alerts to Neumorphism
- Redirect in Login and Home controller to dashboard if user is already authenticated
- Middleware aliases
- Rate limiting for routes
- New global Twig variable "_appApiBaseUrl"

### Fixed
- Wrong version declaration in CHANGELOG.md for 1.2.6
- The docker container "beacon-webserver" write now Nginx logs directly to `docker/logs/*.log` (before the logs was only accessible via `docker logs beacon-webserver`)
- `auth_error` handling in view `auth/login.twig`
- Fixed blank console outputs

### Changed
- Moved app secret generation into separate generator class
- Middleware can now not only be attached to route groups, but also to single routes
- Grouping of web routes
- Changed first paragraph in `resources/views/mail/verify_email.twig`, cause the template is now used for email validation during registration, and changing email under settings
- All web routes have now the CSRF middleware by default

### Changed (Breaking)
- Moved `common_notification.twig` template from `resources/views/auth` to `resources/views`
- Moved verification token generation from `app/Controller/Auth/AuthTrait` to `app/Helpers/StringHelper`
- Unified password field names in registration and account settings page and related DTOs/Controllers
- Middleware implementation and handling
- Attaching middleware to routes and route groups does now happen via aliases instead of classes
- Routes **need** a "name" definition now

## [1.2.6] - 2026-06-18

### Added
- New Neumorphism components:
  - Slider
  - Badges
- Highlighting now all components, which are navigable via TAB key (some components did miss the highlights)

### Fixed
- Fixed `@author` tags in PHP files (added missing > after email address)

### Other
- Removed empty lines from `neumorphism.css` file

## [1.2.5] - 2026-06-18

This is just a re-tagged version 1.2.3 due to some issues with packagist.

## [1.2.4] - 2026-06-18

This is just a re-tagged version 1.2.3 due to some issues with packagist.

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