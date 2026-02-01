<?php
/**
 * Shortcode handler.
 *
 * @package Fancy_Scroll_Anims
 */

namespace Fancy_Scroll_Anims;

defined( 'ABSPATH' ) || die();

/**
 * Handles shortcode registration and rendering.
 */
class Shortcode {

	/**
	 * Register the shortcode.
	 *
	 * @return void
	 */
	public function register(): void {
		add_shortcode( 'fancy_scroll_anim', array( $this, 'render' ) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array<string, mixed> $atts Shortcode attributes.
	 *
	 * @return string Shortcode output.
	 */
	public function render( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'id'    => 0,
				'class' => '',
			),
			$atts,
			'fancy_scroll_anim'
		);

		$post_id = absint( $atts['id'] );

		if ( ! $post_id ) {
			return '';
		}

		// Verify post exists and is correct type.
		$post = get_post( $post_id );

		if ( ! $post || POST_TYPE !== $post->post_type ) {
			return '';
		}

		// Get frame data.
		$frames = get_post_meta( $post_id, META_FRAMES, true );

		if ( empty( $frames ) || ! is_array( $frames ) ) {
			return '';
		}

		// Get settings.
		$easing     = get_post_meta( $post_id, META_EASING, true );
		$loop_count = get_post_meta( $post_id, META_LOOP_COUNT, true );
		$width      = get_post_meta( $post_id, META_FRAME_WIDTH, true );
		$height     = get_post_meta( $post_id, META_FRAME_HEIGHT, true );

		if ( empty( $easing ) ) {
			$easing = DEF_EASING;
		}

		if ( empty( $loop_count ) ) {
			$loop_count = DEF_LOOP_COUNT;
		}

		// Build CSS classes.
		$css_classes  = array( 'fancy-scroll-anim' );
		$custom_class = sanitize_text_field( $atts['class'] );

		if ( ! empty( $custom_class ) ) {
			$css_classes[] = $custom_class;
		}

		// Calculate aspect ratio for auto height.
		$aspect_ratio = '';

		if ( $width && $height ) {
			$aspect_ratio = sprintf( 'aspect-ratio: %d / %d;', absint( $width ), absint( $height ) );
		}

		// Get first frame (frames are indexed from 1).
		$first_frame = reset( $frames );

		// Render container.
		$output = sprintf(
			'<div class="%s" data-anim-id="%d" data-easing="%s" data-loops="%d" data-frames="%s" style="width: 100%%; %s"><img class="fsa-frame" src="%s" alt="%s" style="width: 100%%; height: auto; display: block;" /></div>',
			esc_attr( implode( ' ', $css_classes ) ),
			$post_id,
			esc_attr( $easing ),
			absint( $loop_count ),
			esc_attr( wp_json_encode( array_values( $frames ) ) ),
			esc_attr( $aspect_ratio ),
			esc_url( $first_frame ),
			esc_attr( get_the_title( $post_id ) )
		);

		return $output;
	}
}
