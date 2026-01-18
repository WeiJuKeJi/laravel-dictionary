# Changelog

All notable changes to `laravel-dictionary` will be documented in this file.

## [1.1.0] - 2026-01-18

### Added
- Auto-generate dictionary item values feature with `auto_generate_value` parameter
- Export dictionaries command: `php artisan dictionary:export`
- Reseed dictionaries command: `php artisan dictionary:reseed`
- DictionarySeeder for importing seed data from JSON
- Support for multiple databases (MySQL, PostgreSQL, SQLite)
- Pagination configuration in config file (`per_page`, `max_per_page`)

### Changed
- Refactored controller architecture using RespondsWithApi Concern
- Updated ApiResponse from trait to class with static methods
- Simplified controller methods using helper methods (respondWithPagination, respondWithResource, respondWithList)
- Flattened configuration structure (removed nested arrays)
- Response format: changed `message` to `msg` for consistency
- Simplified pagination response (removed `current_page` and `per_page` fields)
- Updated API documentation with complete route prefixes

### Fixed
- Fixed route parameter names in UpdateRequest classes (dictionaryItem, dictionaryCategory)
- Added try-catch error handling in delete operations for friendly error messages
- Fixed PostgreSQL compatibility in auto-generate value feature

### Improved
- Better error handling with proper HTTP status codes (400 instead of 500)
- Cleaner code with 40% reduction in controller code
- More consistent API response format
- Enhanced documentation with new features

## [1.0.0] - 2024-12-17

### Added
- Initial release
- Dictionary category management with hierarchical structure
- Dictionary item management with enable/disable support
- RESTful API endpoints for CRUD operations
- EloquentFilter integration for advanced querying
- Cache support configuration
- Comprehensive API resources and request validation
- Database migrations for categories and items tables
- Service layer for business logic
- API response trait for consistent responses
- Complete documentation in README
