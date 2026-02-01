# Fancy Scroll Anims

**Contributors:** Elliot Cooke  
**Author URI:** https://cookehouse.net/  
**Version:** 0.2.0  
**Requires at least:** WordPress 6.0  
**Tested up to:** 6.7  
**Requires PHP:** 8.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Add scroll-triggered frame-by-frame animations to your WordPress pages. Create stunning visual effects that respond to user scrolling.

---

## Description

**Fancy Scroll Anims** transforms static images into engaging scroll-triggered animations. Perfect for product showcases, storytelling, and creating immersive web experiences.

### Features

- 🎬 **Frame-by-Frame Animation** - Upload sequential images and create smooth scroll-based animations
- 📜 **Scroll-Triggered** - Animations play as users scroll down and reverse when scrolling up
- ⚙️ **Configurable** - Adjust easing functions and loop counts per animation
- 🎯 **Simple Shortcode** - Easy integration with `[fancy_scroll_anim id="123"]`
- 🖼️ **Format Support** - Works with WebP, JPG, and PNG images
- 🚀 **Performance Optimized** - Uses IntersectionObserver API for efficient scroll detection
- 📱 **Responsive** - Automatically adapts to container width

### How It Works

1. Create a new Scroll Animation in WordPress admin
2. Upload your animation frame images (numbered sequentially)
3. Configure easing and loop settings
4. Copy the generated shortcode
5. Paste into any page or post

Frames are named like: `product-1.webp`, `product-002.jpg`, `product-3.png`  
The animation engine automatically detects and sequences them.

---

## Installation

1. Upload the `fancy-scroll-anims` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Scroll Anims** in the admin menu to create your first animation

---

## Usage

### Creating an Animation

1. Navigate to **Scroll Anims > Add New**
2. Enter a title for your animation
3. Upload frame images (coming in v1.1)
4. Configure easing function and loop count
5. Publish

### Embedding in Pages

Copy the shortcode from the animations list and paste into your page:

```
[fancy_scroll_anim id="123"]
```

**Optional Parameters:**
- `class` - Add custom CSS classes: `[fancy_scroll_anim id="123" class="my-custom-class"]`

---

## File Naming Convention

Animation frames must follow this pattern:

```
animation-name-1.webp
animation-name-002.jpg
animation-name-3.png
```

**Rules:**
- Frame index is the number immediately before the file extension
- The number must be preceded by a `-` character
- Zero-padding is ignored (001, 02, 3 all work)
- Supported extensions: `.webp`, `.jpg`, `.jpeg`, `.png`

---

## Roadmap

### v0.2.0 - Bulk Upload (In Development)
- Drag-and-drop frame upload
- Automatic frame sequencing
- Preview player in admin

### v0.3.0 - Animation Engine
- Full scroll-based playback
- Easing function support
- Loop functionality

### v1.0.0 - Production Release
- Settings page
- Performance optimizations
- Enhanced documentation
- Full testing and polish

---

## Frequently Asked Questions

### How many frames can I upload?

There's no hard limit, but we recommend 30-100 frames for optimal performance.

### Can I use this with Gutenberg blocks?

Yes! Use the Shortcode block and paste your `[fancy_scroll_anim]` code.

### Where are the images stored?

Images are stored in `wp-content/scroll-anims/` outside the media library for better organization.

### Does it work on mobile?

Yes, the animations are fully responsive and work on all devices.

---

## Changelog

### 0.1.0 - 2026-02-01- Refactored main plugin file (removed namespace, added function prefixes)
- Moved constants to main plugin file for clarity
- Added WordPress-compatible readme.txt
- Added CHANGELOG.md for version tracking
- Improved code documentation

### 0.1.0 - 2026-02-01- Initial development release
- Custom post type for animations
- Basic admin interface
- Shortcode system foundation
- Settings (easing, loop count)
- Plugin structure and activation

---

## Development

Built following WordPress coding standards with modern PHP 8.0+ practices.

**Project Structure:**
```
fancy-scroll-anims/
├── fancy-scroll-anims.php    # Main plugin file
├── constants.php              # Plugin constants
├── includes/                  # Core classes
│   ├── class-plugin.php
│   ├── class-post-type.php
│   ├── class-admin-hooks.php
│   ├── class-shortcode.php
│   └── class-frontend.php
├── assets/
│   ├── admin/                # Admin CSS/JS
│   └── public/               # Frontend CSS/JS
└── dev-notes/                # Development documentation
```

See [`dev-notes/00-project-tracker.md`](dev-notes/00-project-tracker.md) for full development roadmap.

---

## Support

For bug reports and feature requests, please use the support forum.

---

## Credits

Created by **Elliot Cooke**  
https://cookehouse.net/

---

## License

This plugin is licensed under the GPLv2 or later.
