<?php
/**
 * Main plugin class.
 *
 * @package Fancy_Scroll_Anims
 */

namespace Fancy_Scroll_Anims;

defined( 'ABSPATH' ) || die();

/**
 * Main plugin orchestrator.
 */
class Plugin {

	/**
	 * Post type handler.
	 *
	 * @var Post_Type|null
	 */
	private ?Post_Type $post_type = null;

	/**
	 * Admin hooks handler.
	 *
	 * @var Admin_Hooks|null
	 */
	private ?Admin_Hooks $admin_hooks = null;

	/**
	 * Shortcode handler.
	 *
	 * @var Shortcode|null
	 */
	private ?Shortcode $shortcode = null;

	/**
	 * Frontend handler.
	 *
	 * @var Frontend|null
	 */
	private ?Frontend $frontend = null;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private string $version;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->version = VERSION;
	}

	/**
	 * Initialize and run the plugin.
	 *
	 * @return void
	 */
	public function run(): void {
		// Load dependencies.
		$this->load_dependencies();

		// Register hooks.
		$this->register_hooks();
	}

	/**
	 * Load plugin dependencies.
	 *
	 * @return void
	 */
	private function load_dependencies(): void {
		require_once FANCY_SCROLL_ANIMS_DIR . 'includes/class-post-type.php';
		require_once FANCY_SCROLL_ANIMS_DIR . 'includes/class-admin-hooks.php';
		require_once FANCY_SCROLL_ANIMS_DIR . 'includes/class-shortcode.php';
		require_once FANCY_SCROLL_ANIMS_DIR . 'includes/class-frontend.php';
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	private function register_hooks(): void {
		// Initialize post type.
		add_action( 'init', array( $this->get_post_type(), 'register' ) );

		// Admin hooks.
		if ( is_admin() ) {
			add_action( 'add_meta_boxes', array( $this->get_admin_hooks(), 'add_meta_boxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this->get_admin_hooks(), 'enqueue_assets' ) );
			add_action( 'save_post_' . POST_TYPE, array( $this->get_admin_hooks(), 'save_settings' ) );

			// AJAX handlers.
			add_action( 'wp_ajax_fsa_upload_frame', array( $this->get_admin_hooks(), 'ajax_upload_frames' ) );
			add_action( 'wp_ajax_fsa_delete_frame', array( $this->get_admin_hooks(), 'ajax_delete_frame' ) );
		}

		// Shortcode.
		add_action( 'init', array( $this->get_shortcode(), 'register' ) );

		// Frontend.
		add_action( 'wp_enqueue_scripts', array( $this->get_frontend(), 'enqueue_assets' ) );
	}

	/**
	 * Get post type handler (lazy loading).
	 *
	 * @return Post_Type
	 */
	public function get_post_type(): Post_Type {
		if ( is_null( $this->post_type ) ) {
			$this->post_type = new Post_Type();
		}
		return $this->post_type;
	}

	/**
	 * Get admin hooks handler (lazy loading).
	 *
	 * @return Admin_Hooks
	 */
	public function get_admin_hooks(): Admin_Hooks {
		if ( is_null( $this->admin_hooks ) ) {
			$this->admin_hooks = new Admin_Hooks( $this->version );
		}
		return $this->admin_hooks;
	}

	/**
	 * Get shortcode handler (lazy loading).
	 *
	 * @return Shortcode
	 */
	public function get_shortcode(): Shortcode {
		if ( is_null( $this->shortcode ) ) {
			$this->shortcode = new Shortcode();
		}
		return $this->shortcode;
	}

	/**
	 * Get frontend handler (lazy loading).
	 *
	 * @return Frontend
	 */
	public function get_frontend(): Frontend {
		if ( is_null( $this->frontend ) ) {
			$this->frontend = new Frontend( $this->version );
		}
		return $this->frontend;
	}
}
