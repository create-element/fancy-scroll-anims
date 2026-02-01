<?php
/**
 * Plugin constants.
 *
 * @package Fancy_Scroll_Anims
 */

namespace Fancy_Scroll_Anims;

defined( 'ABSPATH' ) || die();

// Plugin version.
const VERSION = '0.3.0';

// Upload directory path.
const UPLOAD_DIR_PATH = WP_CONTENT_DIR . '/scroll-anims/';

// Upload directory URL.
define( __NAMESPACE__ . '\UPLOAD_DIR_URL', content_url( '/scroll-anims/' ) );

// Post type slug.
const POST_TYPE = 'fancy_scroll_anim';

// Meta keys - prefix with META_.
const META_FRAMES       = '_fsa_frames';
const META_FRAME_COUNT  = '_fsa_frame_count';
const META_FRAME_WIDTH  = '_fsa_frame_width';
const META_FRAME_HEIGHT = '_fsa_frame_height';
const META_EASING       = '_fsa_easing';
const META_LOOP_COUNT   = '_fsa_loop_count';

// Default values - prefix with DEF_.
const DEF_EASING     = 'linear';
const DEF_LOOP_COUNT = 1;

// Supported image formats.
const SUPPORTED_FORMATS = array( 'webp', 'jpg', 'jpeg', 'png' );

// Nonce actions.
const NONCE_UPLOAD_FRAMES = 'fsa_upload_frames';
const NONCE_DELETE_FRAME  = 'fsa_delete_frame';
