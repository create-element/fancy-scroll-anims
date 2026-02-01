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
		require_once FANCY_SCROLL_ANIMS_DIR . 'admin-templates/meta-box-upload.php';
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
		if ( ! isset( $_POST['fsa_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fsa_settings_nonce'] ) ), 'fsa_save_settings' ) ) {
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
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( NONCE_UPLOAD_FRAMES ),
				'deleteNonce'  => wp_create_nonce( NONCE_DELETE_FRAME ),
				'uploadDirUrl' => UPLOAD_DIR_URL,
				'strings'      => array(
					'uploadError'   => __( 'Upload failed. Please try again.', 'fancy-scroll-anims' ),
					'invalidFormat' => __( 'Invalid file format. Only WebP, JPG, and PNG are supported.', 'fancy-scroll-anims' ),
					'deleteConfirm' => __( 'Are you sure you want to delete this frame?', 'fancy-scroll-anims' ),
					/* translators: %d: Number of files selected */
					'filesSelected' => __( '%d files selected', 'fancy-scroll-anims' ),
				),
			)
		);
	}

	/**
	 * Handle AJAX frame upload.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function ajax_upload_frames(): void {
		check_ajax_referer( NONCE_UPLOAD_FRAMES, 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'fancy-scroll-anims' ) ) );
		}

		// Nonce verified above.
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified above

		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid post ID.', 'fancy-scroll-anims' ) ) );
		}

		// Verify post exists and is correct type.
		$post = get_post( $post_id );

		if ( ! $post || POST_TYPE !== $post->post_type ) {
			wp_send_json_error( array( 'message' => __( 'Invalid animation post.', 'fancy-scroll-anims' ) ) );
		}

		// Check file upload.
		if ( empty( $_FILES['file'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No file uploaded.', 'fancy-scroll-anims' ) ) );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- File upload, validated in validate_frame_file().
		$file = $_FILES['file'];

		// Validate file.
		$validation = $this->validate_frame_file( $file );

		if ( is_wp_error( $validation ) ) {
			wp_send_json_error( array( 'message' => $validation->get_error_message() ) );
		}

		// Parse frame index from filename.
		$frame_index = $this->parse_frame_index( $file['name'] );

		if ( is_wp_error( $frame_index ) ) {
			wp_send_json_error( array( 'message' => $frame_index->get_error_message() ) );
		}

		// Create upload directory for this animation.
		$upload_dir = UPLOAD_DIR_PATH . $post_id . '/';

		if ( ! file_exists( $upload_dir ) ) {
			wp_mkdir_p( $upload_dir );
		}

		// Generate filename.
		$extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		$filename  = sprintf( 'frame-%03d.%s', $frame_index, $extension );
		$filepath  = $upload_dir . $filename;

		// Move uploaded file.
		if ( ! move_uploaded_file( $file['tmp_name'], $filepath ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to save file.', 'fancy-scroll-anims' ) ) );
		}

		// Get image dimensions.
		$image_size = getimagesize( $filepath );
		$width      = $image_size[0] ?? 0;
		$height     = $image_size[1] ?? 0;

		// Update post meta.
		$frames = get_post_meta( $post_id, META_FRAMES, true );

		if ( ! is_array( $frames ) ) {
			$frames = array();
		}

		$file_url               = UPLOAD_DIR_URL . $post_id . '/' . $filename;
		$frames[ $frame_index ] = $file_url;

		// Sort by index.
		ksort( $frames );

		update_post_meta( $post_id, META_FRAMES, $frames );
		update_post_meta( $post_id, META_FRAME_COUNT, count( $frames ) );
		update_post_meta( $post_id, META_FRAME_WIDTH, $width );
		update_post_meta( $post_id, META_FRAME_HEIGHT, $height );

		// phpcs:enable

		wp_send_json_success(
			array(
				'frameIndex' => $frame_index,
				'frameUrl'   => $file_url,
				'frameCount' => count( $frames ),
				'width'      => $width,
				'height'     => $height,
			)
		);
	}

	/**
	 * Handle AJAX frame deletion.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function ajax_delete_frame(): void {
		check_ajax_referer( NONCE_DELETE_FRAME, 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'fancy-scroll-anims' ) ) );
		}

		// Nonce verified above.
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified above

		$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$frame_index = isset( $_POST['frame_index'] ) ? absint( $_POST['frame_index'] ) : null;

		if ( ! $post_id || is_null( $frame_index ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid parameters.', 'fancy-scroll-anims' ) ) );
		}

		// Get frames.
		$frames = get_post_meta( $post_id, META_FRAMES, true );

		if ( ! is_array( $frames ) || ! isset( $frames[ $frame_index ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Frame not found.', 'fancy-scroll-anims' ) ) );
		}

		// Delete file.
		$frame_url  = $frames[ $frame_index ];
		$frame_path = str_replace( UPLOAD_DIR_URL, UPLOAD_DIR_PATH, $frame_url );

		if ( file_exists( $frame_path ) ) {
			wp_delete_file( $frame_path );
		}

		// Remove from array.
		unset( $frames[ $frame_index ] );

		// Update meta.
		update_post_meta( $post_id, META_FRAMES, $frames );
		update_post_meta( $post_id, META_FRAME_COUNT, count( $frames ) );

		// phpcs:enable

		wp_send_json_success(
			array(
				'frameCount' => count( $frames ),
			)
		);
	}

	/**
	 * Validate uploaded frame file.
	 *
	 * @since 0.2.0
	 *
	 * @param array<string, mixed> $file Uploaded file data.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	private function validate_frame_file( array $file ) {
		$result = null;

		// Check upload errors.
		if ( UPLOAD_ERR_OK !== $file['error'] ) {
			$result = new \WP_Error( 'upload_error', __( 'File upload error.', 'fancy-scroll-anims' ) );
		}

		// Check file extension.
		if ( is_null( $result ) ) {
			$extension     = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
			$valid_formats = SUPPORTED_FORMATS;

			if ( ! in_array( $extension, $valid_formats, true ) ) {
				$result = new \WP_Error(
					'invalid_format',
					sprintf(
						/* translators: %s: Comma-separated list of supported formats */
						__( 'Invalid file format. Supported: %s', 'fancy-scroll-anims' ),
						implode( ', ', $valid_formats )
					)
				);
			}
		}

		// Check file size (max 5MB).
		if ( is_null( $result ) && $file['size'] > 5 * 1024 * 1024 ) {
			$result = new \WP_Error( 'file_too_large', __( 'File size exceeds 5MB limit.', 'fancy-scroll-anims' ) );
		}

		// Check mime type.
		if ( is_null( $result ) ) {
			$finfo     = finfo_open( FILEINFO_MIME_TYPE );
			$mime_type = finfo_file( $finfo, $file['tmp_name'] );
			finfo_close( $finfo );

			$valid_mimes = array( 'image/webp', 'image/jpeg', 'image/png' );

			if ( ! in_array( $mime_type, $valid_mimes, true ) ) {
				$result = new \WP_Error( 'invalid_mime', __( 'Invalid file type.', 'fancy-scroll-anims' ) );
			}
		}

		if ( is_null( $result ) ) {
			$result = true;
		}

		return $result;
	}

	/**
	 * Parse frame index from filename.
	 *
	 * @since 0.2.0
	 *
	 * @param string $filename Uploaded filename.
	 *
	 * @return int|\WP_Error Frame index on success, WP_Error on failure.
	 */
	private function parse_frame_index( string $filename ) {
		$result = null;

		// Remove extension.
		$name_without_ext = pathinfo( $filename, PATHINFO_FILENAME );

		// Find last dash.
		$last_dash_pos = strrpos( $name_without_ext, '-' );

		if ( false === $last_dash_pos ) {
			$result = new \WP_Error(
				'invalid_filename',
				__( 'Filename must contain frame number after last dash (e.g., animation-1.webp)', 'fancy-scroll-anims' )
			);
		}

		// Extract number after last dash.
		if ( is_null( $result ) ) {
			$number_part = substr( $name_without_ext, $last_dash_pos + 1 );

			if ( ! is_numeric( $number_part ) ) {
				$result = new \WP_Error(
					'invalid_frame_number',
					__( 'Frame number must be numeric (e.g., animation-1.webp)', 'fancy-scroll-anims' )
				);
			}
		}

		if ( is_null( $result ) ) {
			$frame_index = absint( $number_part );

			if ( $frame_index < 1 ) {
				$result = new \WP_Error(
					'invalid_frame_index',
					__( 'Frame number must be greater than 0.', 'fancy-scroll-anims' )
				);
			}
		}

		if ( is_null( $result ) ) {
			$result = $frame_index;
		}

		return $result;
	}
}
