# Project Tracker

**Version:** 0.4.0
**Last Updated:** 1 February 2026
**Current Phase:** Milestone 5 (Admin Polish)
**Overall Progress:** 80%

---

## Overview

**Fancy Scroll Anims** is a WordPress plugin that adds scroll-triggered frame-by-frame animations to pages. When users scroll down and an animated element comes into view, it plays through its animation frames. When it scrolls out of view, the animation stops.

**Core Features:**
- Custom post type for managing scroll animations
- Bulk image upload for animation frames
- Frame sequencing based on file naming convention
- Shortcode system for embedding animations
- Custom file storage outside WordPress media library
- Support for webp, jpg, and png formats

---

## Active TODO Items

### In Progress
- None

### Up Next
- Bulk frame info display before upload
- Drag-drop frame reordering
- Bulk select and delete frames
- Settings page (global defaults)

### Recently Completed (v0.4.0 - Admin Polish)
- ✅ Copy shortcode button in settings meta box
- ✅ Visual frame count badge with checkmark
- ✅ Animation preview player (play/pause with highlighting)
- ✅ Empty state message for first-time users
- ✅ Dashicon in shortcode admin column

### Recently Completed (v0.3.0)
- ✅ Shortcode rendering with uploaded frames
- ✅ Frontend animation engine with IntersectionObserver
- ✅ Scroll position to frame mapping
- ✅ Bidirectional playback (forward/reverse)
- ✅ All 4 easing functions (linear, ease-in, ease-out, ease-in-out)
- ✅ Fractional loop count support (0.1 to 10.0)
- ✅ requestAnimationFrame optimization
- ✅ Meta box settings save fix
- ✅ Frame preloading for smooth playback
- ✅ PHPCS code standards compliance

### Completed (v0.2.0)
- ✅ Drag-and-drop bulk upload interface
- ✅ AJAX upload with progress tracking
- ✅ File naming parser (extracts frame index)
- ✅ Frame validation (format, size, MIME type)
- ✅ Thumbnail grid with frame numbers
- ✅ Individual frame deletion
- ✅ Automatic frame ordering by index

### Completed (Earlier)
- ✅ Refactored main plugin file (no namespace)
- ✅ Function prefix pattern applied
- ✅ WordPress readme.txt created
- ✅ CHANGELOG.md created
- ✅ Code documentation improvements
- ✅ Main plugin file and structure
- ✅ Custom post type registration
- ✅ Admin meta boxes (settings)
- ✅ Custom upload directory handler
- ✅ Shortcode foundation

---

## Milestones

### Milestone 1: Foundation & Core Structure (100%) ✅
**Goal:** Plugin skeleton, custom post type, file storage

- [x] Create main plugin file (`fancy-scroll-anims.php`)
- [x] Create `constants.php` with plugin constants
- [x] Set up namespace structure (`Fancy_Scroll_Anims`)
- [x] Create main Plugin class with initialization
- [x] Register custom post type `fancy_scroll_anim`
  - [x] Custom icon
  - [x] Labels and capabilities
  - [x] Admin columns (title, shortcode, frame count)
- [x] Create custom upload directory handler (`wp-content/scroll-anims/`)
  - [x] Directory creation on plugin activation
  - [x] Per-animation subdirectories (ready for Milestone 2)
- [x] Basic README.md with plugin description

### Milestone 2: Bulk Image Upload System (100%) ✅
**Goal:** Upload and organize animation frames

- [x] Create admin meta box for bulk upload
- [x] Implement file naming parser
  - [x] Extract frame index from filename (number before extension after last `-`)
  - [x] Validate supported formats (webp, jpg, png)
  - [x] Handle missing/duplicate frame numbers
- [x] Store frame metadata (post meta)
  - [x] Frame paths
  - [x] Frame count
  - [x] Image dimensions
- [x] Display uploaded frames in admin
  - [x] Thumbnail grid
  - [x] Frame order display
  - [x] Delete individual frames option
- [x] Frame reordering interface (automatic by index)
- [x] Animation preview player in post editor
  - [ ] Play/pause controls (deferred to Milestone 4)
  - [ ] Scrubber to test frame sequence (deferred to Milestone 4)

### Milestone 3: Shortcode System (100%) ✅
**Goal:** Embed animations in pages/posts

- [x] Register `[fancy_scroll_anim]` shortcode
- [x] Shortcode parameters:
  - [x] `id` - Animation post ID (required)
  - [x] `class` - Additional CSS classes (optional)
- [x] Generate shortcode in admin (displayed in admin columns)
- [x] Render animation container HTML (width: 100%, height: auto)
- [x] Load frame data via data attributes for JavaScript

### Milestone 4: Frontend Animation Engine (100%) ✅
**Goal:** Scroll-triggered frame playback

- [x] Enqueue frontend JavaScript
- [x] Implement scroll listener with IntersectionObserver
- [x] Calculate scroll progress within viewport
- [x] Map scroll position to frame index
- [x] Preload frame images efficiently
- [x] Handle scroll direction (forward when scrolling down, reverse when scrolling up)
- [x] Implement configurable easing (linear, ease-in, ease-out, ease-in-out)
- [x] Support fractional loop count (0.1 to 10.0 in 0.1 increments)
- [x] Performance optimization (requestAnimationFrame, passive listeners)
- [x] Responsive image handling (aspect-ratio CSS)

### Milestone 5: Admin Experience & Settings (20%)
**Goal:** Polish admin interface and add configuration

- [x] Copy shortcode button in settings meta box
- [x] Frame count visual feedback
- [x] Animation preview player (play/pause)
- [x] Empty state message when no frames
- [x] Dashicon in shortcode admin column
- [ ] Bulk frame info display (before upload starts)
- [ ] Drag-drop frame reordering in grid
- [ ] Bulk select and delete frames
- [ ] Settings page (optional global options)
  - [ ] Default easing function
  - [ ] Default loop count
  - [ ] Performance settings
  - [ ] File storage location (allow customization?)
- [ ] Help documentation tab
- [ ] Bulk actions for animations (delete, duplicate)

### Milestone 6: Testing & Polish (0%)
**Goal:** Production readiness

- [ ] Test with various image formats
- [ ] Test frame naming edge cases
- [ ] Cross-browser testing
- [ ] Mobile responsiveness
- [ ] Performance testing with many animations
- [ ] Security audit (file uploads, nonces)
- [ ] PHPCS code standards compliance
- [ ] Accessibility review
- [ ] Documentation (inline comments, README)

---

## Technical Debt

None yet.

---

## Notes for Development

### File Naming Convention
Animation frames must follow this pattern:
```
animation-name-1.webp
animation-name-002.jpg
animation-name-3.png
```

**Rules:**
- Frame index is the number immediately before the file extension
- The number must be preceded by a `-` character
- Padding is ignored (001, 02, 3 all work)
- Supported extensions: `.webp`, `.jpg`, `.jpeg`, `.png`

### Design Decisions

1. **Animation Playback Behavior:** ✅
   - Frames play forward when scrolling down, reverse when scrolling up
   - Frame 1 shows when element enters viewport (bottom)
   - Final frame shows when element leaves viewport (top)
   - Support configurable loop count (play animation N times within scroll range)

2. **Configurable Properties:** ✅
   - Easing functions: linear (default), ease-in, ease-out, ease-in-out
   - Loop count per animation
   - Per-animation configuration stored in post meta

3. **Preview Functionality:** ✅
   - Preview player in post editor (Milestone 2)
   - Play/pause controls and scrubber

4. **Shortcode Design:** ✅
   - Format: `[fancy_scroll_anim id="123" class="custom-class"]`
   - Width: 100% of container (responsive)
   - Height: auto (maintains aspect ratio)
   - Support custom CSS classes

5. **File Management:** ❓ (Still To Decide)
   - Delete all files when deleting animation post?
   - Orphaned file cleanup utility?

### Architecture Notes

- Use WordPress coding standards (follow `.github/copilot-instructions.md`)
- Store frame metadata as post meta (JSON array of frame paths/indices)
- Use IntersectionObserver API for scroll detection (better performance than scroll events)
- Consider lazy-loading frames to avoid loading all images upfront
- File storage: `wp-content/scroll-anims/{animation-id}/frame-{index}.{ext}`

