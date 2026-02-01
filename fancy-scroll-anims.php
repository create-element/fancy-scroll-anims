<?php
/**
 * Plugin Name:       Fancy Scroll Anims
 * Plugin URI:        https://cookehouse.net/fancy-scroll-anims/
 * Description:       Add scroll-triggered frame-by-frame animations to your WordPress pages. Create stunning visual effects that respond to user scrolling.
 * Version:           0.2.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Elliot Cooke
 * Author URI:        https://cookehouse.net/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       fancy-scroll-anims
 * Domain Path:       /languages
 *
 * @package Fancy_Scroll_Anims
 */

defined( 'ABSPATH' ) || die();

// Plugin URL.
define( 'FANCY_SCROLL_ANIMS_URL', plugin_dir_url( __FILE__ ) );
define( 'FANCY_SCROLL_ANIMS_DIR', __DIR__ . '/' );

// Plugin constants.
require_once __DIR__ . '/constants.php';

// Main plugin class.
require_once FANCY_SCROLL_ANIMS_DIR . 'includes/class-plugin.php';

/**
 * Initialize the plugin.
 *
 * @since 0.1.0
 *
 * @return void
 */
function fancy_scroll_anims_init_plugin(): void {
	global $fancy_scroll_anims_instance;

	$fancy_scroll_anims_instance = new Fancy_Scroll_Anims\Plugin();
	$fancy_scroll_anims_instance->run();
}

/**
 * Get the plugin instance.
 *
 * @since 0.1.0
 *
 * @return Fancy_Scroll_Anims\Plugin Plugin instance.
 */
function fancy_scroll_anims_get_plugin_instance(): Fancy_Scroll_Anims\Plugin {
	global $fancy_scroll_anims_instance;
	return $fancy_scroll_anims_instance;
}

// Initialize on plugins_loaded.
add_action( 'plugins_loaded', 'fancy_scroll_anims_init_plugin' );

/**
 * Plugin activation hook.
 *
 * @since 0.1.0
 *
 * @return void
 */
function fancy_scroll_anims_activate_plugin(): void {
	// Create upload directory.
	$upload_dir = WP_CONTENT_DIR . '/scroll-anims';
	
	if ( ! file_exists( $upload_dir ) ) {
		wp_mkdir_p( $upload_dir );
		
		// Add .htaccess for direct access.
		$htaccess = $upload_dir . '/.htaccess';
		if ( ! file_exists( $htaccess ) ) {
			$content  = '# Fancy Scroll Anims - Allow direct image access' . PHP_EOL;
			$content .= '<IfModule mod_rewrite.c>' . PHP_EOL;
			$content .= 'RewriteEngine Off' . PHP_EOL;
			$content .= '</IfModule>' . PHP_EOL;
			file_put_contents( $htaccess, $content );
		}
		
		// Add index.php for security.
		$index = $upload_dir . '/index.php';
		if ( ! file_exists( $index ) ) {
			file_put_contents( $index, '<?php // Silence is golden.' );
		}
	}
	
	// Flush rewrite rules for custom post type.
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'fancy_scroll_anims_activate_plugin' );

/**
 * Plugin deactivation hook.
 *
 * @since 0.1.0
 *
 * @return void
 */
function fancy_scroll_anims_deactivate_plugin(): void {
	// Flush rewrite rules.
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'fancy_scroll_anims_deactivate_plugin' );
