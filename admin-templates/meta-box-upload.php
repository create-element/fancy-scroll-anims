<?php
/**
 * Admin meta box template for frame upload.
 *
 * @package Fancy_Scroll_Anims
 */

namespace Fancy_Scroll_Anims;

defined( 'ABSPATH' ) || die();

$post_id     = $post->ID ?? 0;
$frames      = get_post_meta( $post_id, META_FRAMES, true );
$frame_count = get_post_meta( $post_id, META_FRAME_COUNT, true );

if ( ! is_array( $frames ) ) {
	$frames = array();
}

wp_nonce_field( 'fsa_upload_frames', 'fsa_upload_nonce' );
?>

<div class="fsa-upload-container">
	
	<?php if ( empty( $frames ) ) : ?>
		<!-- Upload Area (shown when no frames) -->
		<div class="fsa-upload-area" id="fsa-upload-area">
			<div class="fsa-upload-prompt">
				<span class="dashicons dashicons-upload"></span>
				<p><strong><?php esc_html_e( 'Drag and drop animation frames here', 'fancy-scroll-anims' ); ?></strong></p>
				<p class="description"><?php esc_html_e( 'or click to select files', 'fancy-scroll-anims' ); ?></p>
				<p class="description">
					<?php
					printf(
						'%s: %s',
						esc_html__( 'Supported formats', 'fancy-scroll-anims' ),
						esc_html( 'WebP, JPG, PNG' )
					);
					?>
				</p>
			</div>
			<input type="file" id="fsa-file-input" name="fsa_frames[]" multiple accept=".webp,.jpg,.jpeg,.png" style="display:none;">
		</div>

		<!-- Upload Progress -->
		<div class="fsa-upload-progress" id="fsa-upload-progress" style="display:none;">
			<p class="fsa-upload-status"><?php esc_html_e( 'Uploading frames...', 'fancy-scroll-anims' ); ?></p>
			<progress id="fsa-progress-bar" max="100" value="0"></progress>
			<p class="fsa-upload-details">
				<span id="fsa-progress-text">0%</span> - 
				<span id="fsa-upload-file-count"></span>
			</p>
		</div>

	<?php else : ?>
		<!-- Frames Display (shown when frames exist) -->
		<div class="fsa-frames-header">
			<p>
				<?php
				printf(
					'<strong>%s:</strong> %d',
					esc_html__( 'Total Frames', 'fancy-scroll-anims' ),
					absint( $frame_count )
				);
				?>
			</p>
			<button type="button" class="button fsa-add-more-frames" id="fsa-add-more-btn">
				<span class="dashicons dashicons-plus-alt2"></span>
				<?php esc_html_e( 'Add More Frames', 'fancy-scroll-anims' ); ?>
			</button>
			<input type="file" id="fsa-file-input" name="fsa_frames[]" multiple accept=".webp,.jpg,.jpeg,.png" style="display:none;">
		</div>

		<div class="fsa-frames-grid" id="fsa-frames-grid">
			<?php foreach ( $frames as $index => $frame_url ) : ?>
				<?php
				$frame_path = str_replace( UPLOAD_DIR_URL, UPLOAD_DIR_PATH, $frame_url );
				printf(
					'<div class="fsa-frame-item" data-index="%d" data-url="%s"><img src="%s" alt="%s" /><span class="fsa-frame-number">%d</span><button type="button" class="fsa-frame-delete" data-index="%d" title="%s"><span class="dashicons dashicons-no-alt"></span></button></div>',
					absint( $index ),
					esc_url( $frame_url ),
					esc_url( $frame_url ),
					esc_attr( sprintf( __( 'Frame %d', 'fancy-scroll-anims' ), $index + 1 ) ),
					absint( $index + 1 ),
					absint( $index ),
					esc_attr__( 'Delete frame', 'fancy-scroll-anims' )
				);
				?>
			<?php endforeach; ?>
		</div>

		<!-- Upload Progress (for adding more frames) -->
		<div class="fsa-upload-progress" id="fsa-upload-progress" style="display:none;">
			<p class="fsa-upload-status"><?php esc_html_e( 'Uploading frames...', 'fancy-scroll-anims' ); ?></p>
			<progress id="fsa-progress-bar" max="100" value="0"></progress>
			<p class="fsa-upload-details">
				<span id="fsa-progress-text">0%</span> - 
				<span id="fsa-upload-file-count"></span>
			</p>
		</div>

	<?php endif; ?>

	<!-- Error Messages -->
	<div class="fsa-upload-errors" id="fsa-upload-errors" style="display:none;"></div>

</div>
