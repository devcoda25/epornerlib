# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-01

### Added
- Initial release
- Full Eporner API v2 support:
  - Search videos with all parameters
  - Get video by ID
  - Get removed videos list
- PHP 8.0+ support with strict typing
- Guzzle HTTP client integration
- Parameter validation
- Custom exception hierarchy:
  - EpornerException (base)
  - APIException (API errors)
  - ValidationException (parameter errors)
- Model classes:
  - Video
  - Thumb
  - VideoCollection
  - RemovedVideo
- Parameter classes:
  - SearchParams
  - VideoIdParams
- HTTP client wrapper
- Response parser (JSON/XML)
- VideoIterator for automatic pagination
- Helper functions
- Comprehensive README documentation

### Dependencies
- PHP 8.0+
- Guzzle HTTP client 7.0+

## [Unreleased]

### Planned
- PSR-18 HTTP Client implementation
- Laravel integration
- Caching support
- Rate limiting
