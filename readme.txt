=== Fancy Scroll Anims ===
Contributors: elliotcooke
Tags: animation, scroll, scroll animation, scroll effects, frame animation
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add scroll-triggered frame-by-frame animations to your WordPress pages. Create stunning visual effects that respond to user scrolling.

== Description ==

**Fancy Scroll Anims** transforms static images into engaging scroll-triggered animations. Perfect for product showcases, storytelling, and creating immersive web experiences.

= Features =

* 🎬 **Frame-by-Frame Animation** - Upload sequential images and create smooth scroll-based animations
* 📜 **Scroll-Triggered** - Animations play as users scroll down and reverse when scrolling up
* ⚙️ **Configurable** - Adjust easing functions and loop counts per animation
* 🎯 **Simple Shortcode** - Easy integration with `[fancy_scroll_anim id="123"]`
* 🖼️ **Format Support** - Works with WebP, JPG, and PNG images
* 🚀 **Performance Optimized** - Uses IntersectionObserver API for efficient scroll detection
* 📱 **Responsive** - Automatically adapts to container width

= How It Works =

1. Create a new Scroll Animation in WordPress admin
2. Upload your animation frame images (numbered sequentially)
3. Configure easing and loop settings
4. Copy the generated shortcode
5. Paste into any page or post

Frames are named like: `product-1.webp`, `product-002.jpg`, `product-3.png`
The animation engine automatically detects and sequences them.

= File Naming Convention =

Animation frames must follow this pattern:

* Frame index is the number immediately before the file extension
* The number must be preceded by a `-` character
* Zero-padding is ignored (001, 02, 3 all work)
* Supported extensions: `.webp`, `.jpg`, `.jpeg`, `.png`

Examples:
* `animation-name-1.webp`
* `animation-name-002.jpg`
* `animation-name-3.png`

== Installation ==

1. Upload the `fancy-scroll-anims` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Scroll Anims** in the admin menu to create your first animation

== Frequently Asked Questions ==

= How many frames can I upload? =

There's no hard limit, but we recommend 30-100 frames for optimal performance.

= Can I use this with Gutenberg blocks? =

Yes! Use the Shortcode block and paste your `[fancy_scroll_anim]` code.

= Where are the images stored? =

Images are stored in `wp-content/scroll-anims/` outside the media library for better organization.

= Does it work on mobile? =

Yes, the animations are fully responsive and work on all devices.

= What shortcode parameters are available? =

The shortcode accepts the following parameters:
* `id` (required) - The animation post ID
* `class` (optional) - Additional CSS classes for styling

Example: `[fancy_scroll_anim id="123" class="my-custom-class"]`

== Screenshots ==

1. Custom post type admin interface with shortcode column
2. Animation settings meta box with easing and loop options
3. Example scroll animation on frontend

== Changelog ==

= 0.2.0 - 2026-02-01 =
* Refactored main plugin file to follow WordPress coding standards
* Moved constants to main plugin file
* Added WordPress readme.txt
* Added CHANGELOG.md for version tracking
* Improved code documentation

= 0.1.0 - 2026-02-01 =
* Initial development release
* Custom post type for animations
* Basic admin interface with settings
* Shortcode system foundation
* Easing function support (linear, ease-in, ease-out, ease-in-out)
* Loop count configuration
* Plugin structure and activation
* Custom upload directory setup

== Upgrade Notice ==

= 0.2.0 =
Improved code structure and WordPress coding standards compliance. Safe to upgrade.

== Development ==

This plugin is actively developed on GitHub. Contributions and bug reports are welcome.

Project repository: https://github.com/create-element/fancy-scroll-anims

== Credits ==

Created by Elliot Cooke
Website: https://cookehouse.net/

== License ==

This plugin is licensed under the GPLv2 or later.
