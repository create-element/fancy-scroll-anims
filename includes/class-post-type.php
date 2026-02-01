<?php
/**
 * Custom post type registration.
 *
 * @package Fancy_Scroll_Anims
 */

namespace Fancy_Scroll_Anims;

defined( 'ABSPATH' ) || die();

/**
 * Handles custom post type registration.
 */
class Post_Type {

	/**
	 * Register the custom post type.
	 *
	 * @return void
	 */
	public function register(): void {
		$labels = array(
			'name'                  => _x( 'Scroll Animations', 'Post type general name', 'fancy-scroll-anims' ),
			'singular_name'         => _x( 'Scroll Animation', 'Post type singular name', 'fancy-scroll-anims' ),
			'menu_name'             => _x( 'Scroll Anims', 'Admin Menu text', 'fancy-scroll-anims' ),
			'name_admin_bar'        => _x( 'Scroll Animation', 'Add New on Toolbar', 'fancy-scroll-anims' ),
			'add_new'               => __( 'Add New', 'fancy-scroll-anims' ),
			'add_new_item'          => __( 'Add New Scroll Animation', 'fancy-scroll-anims' ),
			'new_item'              => __( 'New Scroll Animation', 'fancy-scroll-anims' ),
			'edit_item'             => __( 'Edit Scroll Animation', 'fancy-scroll-anims' ),
			'view_item'             => __( 'View Scroll Animation', 'fancy-scroll-anims' ),
			'all_items'             => __( 'All Animations', 'fancy-scroll-anims' ),
			'search_items'          => __( 'Search Scroll Animations', 'fancy-scroll-anims' ),
			'parent_item_colon'     => __( 'Parent Scroll Animations:', 'fancy-scroll-anims' ),
			'not_found'             => __( 'No scroll animations found.', 'fancy-scroll-anims' ),
			'not_found_in_trash'    => __( 'No scroll animations found in Trash.', 'fancy-scroll-anims' ),
			'featured_image'        => _x( 'Animation Preview', 'Overrides the "Featured Image" phrase', 'fancy-scroll-anims' ),
			'set_featured_image'    => _x( 'Set preview image', 'Overrides the "Set featured image" phrase', 'fancy-scroll-anims' ),
			'remove_featured_image' => _x( 'Remove preview image', 'Overrides the "Remove featured image" phrase', 'fancy-scroll-anims' ),
			'use_featured_image'    => _x( 'Use as preview image', 'Overrides the "Use as featured image" phrase', 'fancy-scroll-anims' ),
			'archives'              => _x( 'Scroll Animation archives', 'The post type archive label', 'fancy-scroll-anims' ),
			'insert_into_item'      => _x( 'Insert into scroll animation', 'Overrides the "Insert into post" phrase', 'fancy-scroll-anims' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this scroll animation', 'Overrides the "Uploaded to this post" phrase', 'fancy-scroll-anims' ),
			'filter_items_list'     => _x( 'Filter scroll animations list', 'Screen reader text for the filter links', 'fancy-scroll-anims' ),
			'items_list_navigation' => _x( 'Scroll animations list navigation', 'Screen reader text for the pagination', 'fancy-scroll-anims' ),
			'items_list'            => _x( 'Scroll animations list', 'Screen reader text for the items list', 'fancy-scroll-anims' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-images-alt2',
			'supports'           => array( 'title', 'thumbnail' ),
		);

		register_post_type( POST_TYPE, $args );

		// Add custom columns.
		add_filter( 'manage_' . POST_TYPE . '_posts_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_' . POST_TYPE . '_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
	}

	/**
	 * Add custom columns to post list.
	 *
	 * @param array<string, string> $columns Existing columns.
	 *
	 * @return array<string, string> Modified columns.
	 */
	public function add_custom_columns( array $columns ): array {
		$new_columns = array();

		// Add checkbox and title.
		$new_columns['cb']    = $columns['cb'];
		$new_columns['title'] = $columns['title'];

		// Add custom columns.
		$new_columns['shortcode']   = __( 'Shortcode', 'fancy-scroll-anims' );
		$new_columns['frame_count'] = __( 'Frames', 'fancy-scroll-anims' );
		$new_columns['dimensions']  = __( 'Dimensions', 'fancy-scroll-anims' );

		// Add date.
		$new_columns['date'] = $columns['date'];

		return $new_columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 *
	 * @return void
	 */
	public function render_custom_columns( string $column, int $post_id ): void {
		$result = null;

		if ( 'shortcode' === $column ) {
			$result = sprintf(
				'<code class="fsa-shortcode" style="cursor:pointer;" onclick="navigator.clipboard.writeText(\'[fancy_scroll_anim id=&quot;%d&quot;]\'); this.style.background=\'#d4edda\';" title="%s">[fancy_scroll_anim id="%d"]</code>',
				$post_id,
				esc_attr__( 'Click to copy', 'fancy-scroll-anims' ),
				$post_id
			);
		}

		if ( 'frame_count' === $column ) {
			$frame_count = get_post_meta( $post_id, META_FRAME_COUNT, true );
			$result      = $frame_count ? absint( $frame_count ) : '—';
		}

		if ( 'dimensions' === $column ) {
			$width  = get_post_meta( $post_id, META_FRAME_WIDTH, true );
			$height = get_post_meta( $post_id, META_FRAME_HEIGHT, true );

			if ( $width && $height ) {
				$result = sprintf( '%d × %d px', absint( $width ), absint( $height ) );
			}

			if ( is_null( $result ) ) {
				$result = '—';
			}
		}

		if ( ! is_null( $result ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped via sprintf/esc_* functions above.
			echo $result;
		}
	}
}
