# Changelog

All notable changes to Fancy Scroll Anims will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.3.0] - 2026-02-01

### Added
- Bulk image upload interface with drag-and-drop support
- Real-time AJAX upload with progress bar and percentage tracking
- File naming parser to extract frame index from filenames (e.g., `product-5.webp` → frame 5)
- Comprehensive file validation (format, size, MIME type)
- Thumbnail grid display showing all uploaded frames
- Frame number badges on thumbnails
- Individual frame deletion with confirmation dialog
- "Add More Frames" functionality to existing animations
- Automatic frame ordering by extracted index
- Frame metadata storage in post meta (`_fsa_frames`, `_fsa_frame_count`, `_fsa_frame_width`, `_fsa_frame_height`)
- Admin template system for meta boxes (`admin-templates/meta-box-upload.php`)
- AJAX endpoints: `wp_ajax_fsa_upload_frame` and `wp_ajax_fsa_delete_frame`

### Changed
- Enhanced admin JavaScript with ES6 class-based upload handler
- Improved admin CSS with progress bar styling and grid layout
- Updated Admin_Hooks class with upload/delete methods and validation logic

### Technical Details
- Uses native XMLHttpRequest for upload progress events (no external libraries)
- HTML5 File API for drag-and-drop
- File size limit: 5MB per frame
- Supported formats: WebP, JPG, JPEG, PNG
- Files stored in `wp-content/scroll-anims/{post-id}/frame-{index}.{ext}`
- Automatic zero-padding in filenames (frame-001.webp)

## [0.2.0] - 2026-02-01

### Changed
- Refactored main plugin file to remove namespace and use function prefixes
- Moved `FANCY_SCROLL_ANIMS_URL` and `FANCY_SCROLL_ANIMS_DIR` constants to main plugin file
- Updated coding standards to match WordPress best practices
- Improved code documentation with `@since` tags

### Added
- WordPress-compatible `readme.txt` file
- GitHub `CHANGELOG.md` file
- Better separation of concerns between main file and constants

### Fixed
- Parse error caused by temporary typo in main plugin file

## [0.1.0] - 2026-02-01

### Added
- Initial development release
- Custom post type `fancy_scroll_anim` for managing scroll animations
- Admin interface with meta boxes for settings
- Settings meta box with easing function selector (linear, ease-in, ease-out, ease-in-out)
- Loop count configuration (1-10 loops per scroll pass)
- Shortcode system `[fancy_scroll_anim id="123"]`
- Custom CSS class support via shortcode
- Custom upload directory at `wp-content/scroll-anims/`
- Admin columns for shortcode (click-to-copy), frame count, and dimensions
- Plugin activation/deactivation hooks
- Asset loading structure (CSS/JS for admin and frontend)
- Lazy loading pattern for plugin components
- Global plugin instance pattern
- Constants file with all plugin constants
- Comprehensive README.md
- Development documentation in `dev-notes/`
- Project tracker for milestone management

### Technical Details
- PHP 8.0+ with type hints and return types
- WordPress 6.0+ compatibility
- Namespace structure: `Fancy_Scroll_Anims`
- Function prefixes: `fancy_scroll_anims_`
- Follows WordPress Coding Standards
- Security: Nonce verification, input sanitization, output escaping
- Performance: IntersectionObserver ready for scroll detection (Milestone 4)

### Files Created
- `fancy-scroll-anims.php` - Main plugin file
- `constants.php` - Plugin constants
- `includes/class-plugin.php` - Main orchestrator
- `includes/class-post-type.php` - Custom post type registration
- `includes/class-admin-hooks.php` - Admin functionality
- `includes/class-shortcode.php` - Shortcode handler
- `includes/class-frontend.php` - Frontend assets
- `assets/admin/admin.css` - Admin styles
- `assets/admin/admin.js` - Admin scripts
- `assets/public/style.css` - Frontend styles
- `assets/public/animation.js` - Animation engine (stub)
- `README.md` - Project documentation
- `dev-notes/00-project-tracker.md` - Development roadmap

## [Unreleased]

### Planned for v0.4.0 - Animation Engine
- Full scroll-based playback implementation
- IntersectionObserver integration
- Scroll progress calculation
- Frame preloading strategy
- Bidirectional playback (forward/reverse)
- Easing function implementation
- Loop count execution
- Performance optimization (RAF, debouncing)
- Responsive image handling

### Planned for v1.0.0 - Production Release
- Settings page with global options
- Help documentation tab
- Bulk actions for animations
- Full testing suite
- Cross-browser testing
- Mobile responsiveness verification
- Performance benchmarking
- Security audit
- PHPCS compliance verification
- Accessibility review
- Complete inline documentation

---

**Note:** This project follows WordPress coding standards and uses modern PHP 8.0+ features.
For detailed development patterns, see `.github/copilot-instructions.md` and `dev-notes/patterns/`.
