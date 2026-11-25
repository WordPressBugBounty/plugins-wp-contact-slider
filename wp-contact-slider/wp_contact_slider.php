<?php
/**
 * Plugin Name: WP Contact Slider
 * Plugin URI: https://wpcontactslider.com/
 * Description: Simple Contact Slider to display Contact Form 7, Gravity Forms, some other shortcodes and display random Text or HTML.
 * Version: 2.5.6
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Author: wpexpertsio
 * Author URI: https://wpcontactslider.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-contact-slider
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WPCS_VERSION' ) ) {
	define( 'WPCS_VERSION', '2.5.6' );
}

if ( is_admin() ) {

	// To display admin notice
	require_once 'inc/wpcs_update_notice.php';

}

if ( ! function_exists( 'str_starts_with' ) ) {
	/**
	 * PHP 8 polyfill so bundled Meta Box can run on PHP 7.4.
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle   Needle.
	 * @return bool
	 */
	function str_starts_with( $haystack, $needle ) {
		$haystack = (string) $haystack;
		$needle   = (string) $needle;
		if ( '' === $needle ) {
			return true;
		}
		return 0 === strncmp( $haystack, $needle, strlen( $needle ) );
	}
}

if ( ! function_exists( 'str_ends_with' ) ) {
	/**
	 * @param string $haystack Haystack.
	 * @param string $needle   Needle.
	 * @return bool
	 */
	function str_ends_with( $haystack, $needle ) {
		$haystack = (string) $haystack;
		$needle   = (string) $needle;
		if ( '' === $needle ) {
			return true;
		}
		return substr( $haystack, -strlen( $needle ) ) === $needle;
	}
}

if ( ! function_exists( 'str_contains' ) ) {
	/**
	 * @param string $haystack Haystack.
	 * @param string $needle   Needle.
	 * @return bool
	 */
	function str_contains( $haystack, $needle ) {
		$needle = (string) $needle;
		if ( '' === $needle ) {
			return true;
		}
		return false !== strpos( (string) $haystack, $needle );
	}
}

/**
 * Loads MetaBox\ integration classes from inc/meta-box/src (Support\ via Composer vendor).
 *
 * @param string $class Class name.
 */
function wpcs_autoload_meta_box( $class ) {
	$prefix = 'MetaBox\\';
	if ( 0 !== strpos( $class, $prefix ) ) {
		return;
	}
	$relative = substr( $class, strlen( $prefix ) );
	$file     = __DIR__ . '/inc/meta-box/src/' . str_replace( '\\', '/', $relative ) . '.php';
	if ( is_readable( $file ) ) {
		require_once $file;
	}
}

/**
 * Whether the current admin request is for a slider screen.
 *
 * Screen is not available this early, so query vars identify the post type only (no state change).
 *
 * @return bool
 */
function wpcs_is_wpcs_admin_request() {
	if ( ! is_admin() ) {
		return false;
	}

	$post_type = filter_input( INPUT_GET, 'post_type', FILTER_CALLBACK, array( 'options' => 'sanitize_key' ) );
	if ( 'wpcs' === $post_type ) {
		return true;
	}

	$post_id = filter_input( INPUT_GET, 'post', FILTER_VALIDATE_INT );
	if ( $post_id && 'wpcs' === get_post_type( $post_id ) ) {
		return true;
	}

	$post_id = filter_input( INPUT_POST, 'post_ID', FILTER_VALIDATE_INT );
	return $post_id && 'wpcs' === get_post_type( $post_id );
}

/**
 * Whether the request is a bundled Meta Box admin-ajax call (e.g. post field search).
 *
 * @return bool
 */
function wpcs_is_meta_box_ajax_request() {
	if ( ! wp_doing_ajax() ) {
		return false;
	}

	if ( ! isset( $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Action name only; Meta Box verifies nonces in handlers.
		return false;
	}

	$action = sanitize_key( wp_unslash( $_REQUEST['action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Action name only.

	return '' !== $action && 0 === strpos( $action, 'rwmb_' );
}

/**
 * Load bundled Meta Box and slider meta fields on wpcs admin requests only.
 */
function wpcs_load_meta_box_admin() {
	if ( ! wpcs_is_wpcs_admin_request() && ! wpcs_is_meta_box_ajax_request() ) {
		return;
	}

	spl_autoload_register( 'wpcs_autoload_meta_box' );

	if ( ! class_exists( 'RW_Meta_Box' ) ) {
		require_once __DIR__ . '/inc/meta-box/meta-box.php';
	}

	require_once __DIR__ . '/inc/wpcs_meta_fields.php';
}
add_action( 'plugins_loaded', 'wpcs_load_meta_box_admin', 1 );

// For admin functions support
require_once 'inc/wpcs_admin_functions.php';

// For slider related functions
require_once 'inc/wpcs_slider.php';

// To call front end functions
require_once 'inc/wpcs_frontend_functions.php';

// Get CSS
require_once 'inc/wpcs_enque_styles.php';

// Get Scripts
require_once 'inc/wpcs_enque_scripts.php';

// Add-ons bundle sub menu
require_once 'inc/wpcs_bundle_menu.php';

if ( ! function_exists( 'wpcs_allowed_html' ) ) :
	function wpcs_allowed_html() {

		$allowed_atts                = array(
			'align'      => array(),
			'class'      => array(),
			'type'       => array(),
			'id'         => array(),
			'dir'        => array(),
			'lang'       => array(),
			'style'      => array(),
			'xml:lang'   => array(),
			'src'        => array(),
			'alt'        => array(),
			'href'       => array(),
			'rel'        => array(),
			'rev'        => array(),
			'target'     => array(),
			'novalidate' => array(),
			'type'       => array(),
			'value'      => array(),
			'required'   => array(),
			'name'       => array(),
			'tabindex'   => array(),
			'action'     => array(),
			'method'     => array(),
			'for'        => array(),
			'width'      => array(),
			'height'     => array(),
			'data-editor' => array(),
			'data'     	 => array(),
			'title'      => array(),
			'value'      => array(),
			'selected'   => array(),
			'enctype'    => array(),
			'disable'    => array(),
			'disabled'   => array(),
			'noscript'   => array(),
			'checked'	 => array(),
			'autocomplete' => array(),
			'aria-label' => array(),
			'media'		 => array(),
			'data-wp-editor-id' => array(),
			'rows'		 => array(),
			'cols'		 => array(),
			'aria-hidden' => array(),
			'role'		 => array(),
			'hidefocus'	 => array(),
			'tabindex'	 => array(),
			'aria-pressed' => array(),
			'aria-disabled' => array(),
			'frameborder' => array(),
			'allowtransparency' => array(),
			'size'		 => array(),
			'data-options' => array(),
			'aria-haspopup' => array(),
			'aria-expanded' => array(),
			'aria-labelledby' => array(),
			'aria-describedby' => array(),
			'data-selected' => array(),
			'placeholder' => array(),
			'data-autosave' => array(),
			'data-object-type' => array(),
			'data-object-id' => array()
		);
		$allowed_atts = apply_filters('wpcs_allowed_atts', $allowed_atts);

		$allowedposttags['font']     = $allowed_atts;
		$allowedposttags['form']     = $allowed_atts;
		$allowedposttags['link']     = $allowed_atts;
		$allowedposttags['required'] = $allowed_atts;
		$allowedposttags['noscript'] = $allowed_atts;
		$allowedposttags['label']    = $allowed_atts;
		$allowedposttags['select']   = $allowed_atts;
		$allowedposttags['option']   = $allowed_atts;
		$allowedposttags['input']    = $allowed_atts;
		$allowedposttags['button']    = $allowed_atts;
		$allowedposttags['textarea'] = $allowed_atts;
		$allowedposttags['strong']   = $allowed_atts;
		$allowedposttags['small']    = $allowed_atts;
		$allowedposttags['table']    = $allowed_atts;
		$allowedposttags['span']     = $allowed_atts;
		$allowedposttags['abbr']     = $allowed_atts;
		$allowedposttags['code']     = $allowed_atts;
		$allowedposttags['pre']      = $allowed_atts;
		$allowedposttags['div']      = $allowed_atts;
		$allowedposttags['img']      = $allowed_atts;
		$allowedposttags['h1']       = $allowed_atts;
		$allowedposttags['h2']       = $allowed_atts;
		$allowedposttags['h3']       = $allowed_atts;
		$allowedposttags['h4']       = $allowed_atts;
		$allowedposttags['h5']       = $allowed_atts;
		$allowedposttags['h6']       = $allowed_atts;
		$allowedposttags['ol']       = $allowed_atts;
		$allowedposttags['ul']       = $allowed_atts;
		$allowedposttags['li']       = $allowed_atts;
		$allowedposttags['em']       = $allowed_atts;
		$allowedposttags['hr']       = $allowed_atts;
		$allowedposttags['br']       = $allowed_atts;
		$allowedposttags['tr']       = $allowed_atts;
		$allowedposttags['td']       = $allowed_atts;
		$allowedposttags['p']        = $allowed_atts;
		$allowedposttags['a']        = $allowed_atts;
		$allowedposttags['b']        = $allowed_atts;
		$allowedposttags['i']        = $allowed_atts;
		
		$allowedposttags = apply_filters('wpcs_allowed_html', $allowedposttags, $allowed_atts );		
		return $allowedposttags;
	}
endif;

/* Register activation hook. */
register_activation_hook( __FILE__, 'wpcs_admin_notice_activation_hook' );

/**
 * Runs only when the plugin is activated.
 *
 * @since 2.2
 */
function wpcs_admin_notice_activation_hook() {

	if ( get_option( 'wpcs_display_notice_2_2' ) === 'no' ) { // Here we can leave it as it is even if future - as this delay is required for only new users
		return '';
	}

	/* Create transient data */
	set_transient( 'wpcs-admin-notice-2-2-hold', true, 24 * HOUR_IN_SECONDS );

}

// Create a helper function for easy SDK access.
function wpcs_fs() {
	global $wpcs_fs;

	if ( ! isset( $wpcs_fs ) ) {
		// Include Freemius SDK.
		require_once dirname( __FILE__ ) . '/freemius/start.php';

		$wpcs_fs = fs_dynamic_init(
			array(
				'id'             => '1355',
				'slug'           => 'wp-contact-slider',
				'type'           => 'plugin',
				'public_key'     => 'pk_8c7f1aab720c6d8cbfa2c2cb0f7a0',
				'is_premium'     => false,
				'has_addons'     => false,
				'has_paid_plans' => false,
				'menu'           => array(
					'slug'    => 'edit.php?post_type=wpcs',
					'contact' => false,
				),
			)
		);
	}

	return $wpcs_fs;
}

// Init Freemius.
wpcs_fs();
// Signal that SDK was initiated.
do_action( 'wpcs_fs_loaded' );
