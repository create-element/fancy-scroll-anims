<?php
/**
 * Admin hooks and meta boxes.
 *
 * @package Fancy_Scroll_Anims
 */

namespace Fancy_Scroll_Anims;

defined( 'ABSPATH' ) || die();

/**
 * Handles admin functionality.
 */
class Admin_Hooks {

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
	 * Add meta boxes to the post edit screen.
	 *
	 * @return void
	 */
	public function add_meta_boxes(): void {
		add_meta_box(
			'fsa_frames_upload',
			__( 'Animation Frames', 'fancy-scroll-anims' ),
			array( $this, 'render_frames_meta_box' ),
			POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'fsa_settings',
			__( 'Animation Settings', 'fancy-scroll-anims' ),
			array( $this, 'render_settings_meta_box' ),
			POST_TYPE,
			'side',
			'default'
		);
	}

	/**
	 * Render frames upload meta box.
	 *
	 * @param \WP_Post $post Current post object.
	 *
	 * @return void
	 */
	public function render_frames_meta_box( \WP_Post $post ): void {
		printf(
			'<p>%s</p>',
			esc_html__( 'Frame upload functionality coming in Milestone 2.', 'fancy-scroll-anims' )
		);
	}

	/**
	 * Render settings meta box.
	 *
	 * @param \WP_Post $post Current post object.
	 *
	 * @return void
	 */
	public function render_settings_meta_box( \WP_Post $post ): void {
		wp_nonce_field( 'fsa_save_settings', 'fsa_settings_nonce' );

		$easing     = get_post_meta( $post->ID, META_EASING, true );
		$loop_count = get_post_meta( $post->ID, META_LOOP_COUNT, true );

		if ( empty( $easing ) ) {
			$easing = DEF_EASING;
		}

		if ( empty( $loop_count ) ) {
			$loop_count = DEF_LOOP_COUNT;
		}

		printf(
			'<p><label for="fsa_easing"><strong>%s</strong></label><br><select id="fsa_easing" name="fsa_easing" style="width:100%%;"><option value="linear"%s>%s</option><option value="ease-in"%s>%s</option><option value="ease-out"%s>%s</option><option value="ease-in-out"%s>%s</option></select></p>',
			esc_html__( 'Easing Function:', 'fancy-scroll-anims' ),
			selected( $easing, 'linear', false ),
			esc_html__( 'Linear', 'fancy-scroll-anims' ),
			selected( $easing, 'ease-in', false ),
			esc_html__( 'Ease In', 'fancy-scroll-anims' ),
			selected( $easing, 'ease-out', false ),
			esc_html__( 'Ease Out', 'fancy-scroll-anims' ),
			selected( $easing, 'ease-in-out', false ),
			esc_html__( 'Ease In/Out', 'fancy-scroll-anims' )
		);

		printf(
			'<p><label for="fsa_loop_count"><strong>%s</strong></label><br><input type="number" id="fsa_loop_count" name="fsa_loop_count" value="%d" min="1" max="10" style="width:100%%;" /></p><p class="description">%s</p>',
			esc_html__( 'Loop Count:', 'fancy-scroll-anims' ),
			absint( $loop_count ),
			esc_html__( 'Number of times to play the animation within the scroll range.', 'fancy-scroll-anims' )
		);

		// Save handler.
		add_action( 'save_post_' . POST_TYPE, array( $this, 'save_settings' ) );
	}

	/**
	 * Save animation settings.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function save_settings( int $post_id ): void {
		// Verify nonce.
		if ( ! isset( $_POST['fsa_settings_nonce'] ) || ! wp_verify_nonce( $_POST['fsa_settings_nonce'], 'fsa_save_settings' ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Nonce verified above.
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified above

		// Save easing.
		if ( isset( $_POST['fsa_easing'] ) ) {
			$easing        = sanitize_text_field( wp_unslash( $_POST['fsa_easing'] ) );
			$valid_easings = array( 'linear', 'ease-in', 'ease-out', 'ease-in-out' );

			if ( in_array( $easing, $valid_easings, true ) ) {
				update_post_meta( $post_id, META_EASING, $easing );
			}
		}

		// Save loop count.
		if ( isset( $_POST['fsa_loop_count'] ) ) {
			$loop_count = absint( $_POST['fsa_loop_count'] );

			if ( $loop_count > 0 && $loop_count <= 10 ) {
				update_post_meta( $post_id, META_LOOP_COUNT, $loop_count );
			}
		}

		// phpcs:enable
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook_suffix Current admin page.
	 *
	 * @return void
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		// Only load on our post type edit screens.
		$current_screen = get_current_screen();

		if ( ! $current_screen || POST_TYPE !== $current_screen->post_type ) {
			return;
		}

		wp_enqueue_style(
			'fancy-scroll-anims-admin',
			FANCY_SCROLL_ANIMS_URL . 'assets/admin/admin.css',
			array(),
			$this->version
		);

		wp_enqueue_script(
			'fancy-scroll-anims-admin',
			FANCY_SCROLL_ANIMS_URL . 'assets/admin/admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			'fancy-scroll-anims-admin',
			'fsaAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( NONCE_UPLOAD_FRAMES ),
			)
		);
	}
}
