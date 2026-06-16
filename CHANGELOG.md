# Changelog

All notable changes to this project will be documented in this file.

## [1.2.0] - 2026-05-14

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