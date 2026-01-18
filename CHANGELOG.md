# Changelog

All notable changes to `laravel-dictionary` will be documented in this file.

## [1.2.0] - 2026-01-18

### Added
- Custom exception class `DictionaryException` for better error handling
- Complete caching implementation with automatic cache invalidation
- Tree structure now includes `count` field showing dictionary item count for each category
- New service methods: `getItemsByKey()`, `getItemValue()`, `refreshCache()`
- Database transaction support for all write operations
- Chinese documentation (README_CN.md)

### Changed
- **Breaking**: Changed auto-generate from `item_value` to `item_key` (keys are now auto-generated numbers)
- **Breaking**: `item_key` is now the numeric identifier, `item_value` is the display text
- Optimized tree building to avoid N+1 queries (single query + in-memory construction)
- Updated Facade methods to match actual implementation
- All exceptions now use `DictionaryException` instead of generic `\Exception`

### Fixed
- Fixed `Undefined array key "item_key"` error when using auto-generate feature
- Fixed tree cache not being cleared when creating/updating/deleting dictionary items
- Fixed N+1 query problem in tree structure building

### Improved
- Performance: Tree structure with item counts requires only 2 database queries
- Cache strategy: Automatic cache invalidation on data changes
- Better error messages with specific exception types
- Transaction protection for data consistency

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
