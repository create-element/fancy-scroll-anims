<?php
/**
 * Frontend functionality.
 *
 * @package Fancy_Scroll_Anims
 */

namespace Fancy_Scroll_Anims;

defined( 'ABSPATH' ) || die();

/**
 * Handles frontend assets and functionality.
 */
class Frontend {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private string $version;

	/**
	 * Constructor.
	 *
	 * @param string $version Plugin version.
	 */
	public function __construct( string $version ) {
		$this->version = $version;
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		wp_enqueue_style(
			'fancy-scroll-anims',
			FANCY_SCROLL_ANIMS_URL . 'assets/public/style.css',
			array(),
			$this->version
		);

		wp_enqueue_script(
			'fancy-scroll-anims',
			FANCY_SCROLL_ANIMS_URL . 'assets/public/animation.js',
			array(),
			$this->version,
			true
		);
	}
}
