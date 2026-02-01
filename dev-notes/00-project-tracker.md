# Project Tracker

**Version:** 0.2.0
**Last Updated:** 1 February 2026
**Current Phase:** Milestone 2 (Bulk Image Upload System)
**Overall Progress:** 17%

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
- Implement bulk image uploader
- File naming parser
- Frame metadata storage

### Recently Completed (v0.2.0)
- ✅ Refactored main plugin file (no namespace)
- ✅ Function prefix pattern applied
- ✅ WordPress readme.txt created
- ✅ CHANGELOG.md created
- ✅ Code documentation improvements

### Completed (v0.1.0)
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

### Milestone 2: Bulk Image Upload System (0%)
**Goal:** Upload and organize animation frames

- [ ] Create admin meta box for bulk upload
- [ ] Implement file naming parser
  - [ ] Extract frame index from filename (number before extension after last `-`)
  - [ ] Validate supported formats (webp, jpg, png)
  - [ ] Handle missing/duplicate frame numbers
- [ ] Store frame metadata (post meta)
  - [ ] Frame paths
  - [ ] Frame count
  - [ ] Image dimensions
- [ ] Display uploaded frames in admin
  - [ ] Thumbnail grid
  - [ ] Frame order display
  - [ ] Delete individual frames option
- [ ] Frame reordering interface (optional drag-drop)
- [ ] Animation preview player in post editor
  - [ ] Play/pause controls
  - [ ] Scrubber to test frame sequence

### Milestone 3: Shortcode System (0%)
**Goal:** Embed animations in pages/posts

- [ ] Register `[fancy_scroll_anim]` shortcode
- [ ] Shortcode parameters:
  - [ ] `id` - Animation post ID (required)
  - [ ] `class` - Additional CSS classes (optional)
- [ ] Generate shortcode in admin (copy-to-clipboard)
- [ ] Render animation container HTML (width: 100%, height: auto)
- [ ] Load only required animation data (avoid loading all frames upfront)

### Milestone 4: Frontend Animation Engine (0%)
**Goal:** Scroll-triggered frame playback

- [ ] Enqueue frontend JavaScript
- [ ] Implement scroll listener with IntersectionObserver
- [ ] Calculate scroll progress within viewport
- [ ] Map scroll position to frame index
- [ ] Preload frame images efficiently
- [ ] Handle scroll direction (forward when scrolling down, reverse when scrolling up)
- [ ] Implement configurable easing (linear, ease-in, ease-out, ease-in-out)
- [ ] Support loop count (play animation N times within scroll range)
- [ ] Performance optimization (debouncing, RAF)
- [ ] Responsive image handling

### Milestone 5: Admin Experience & Settings (0%)
**Goal:** Polish admin interface and add configuration

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

