<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * css/style.css is intentionally not enqueued.
 * It changes body overflow and targets an older markup. Slider CSS is inline on wpcs-front.
 */


function wpcs_enque_backend_styles() {

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'wpcs' !== $screen->post_type ) {
		return;
	}

	$plugin_root = dirname( __DIR__ );
	$css_path    = $plugin_root . '/css/admin-style.css';
	$css_url     = plugins_url( 'css/admin-style.css', $plugin_root . '/wp_contact_slider.php' );
	$version     = file_exists( $css_path ) ? (string) filemtime( $css_path ) : '1.0.0';

	wp_enqueue_style( 'wpcs-admin', $css_url, array(), $version );

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$addons = array(
		'wpcs-advance-settings-addon-premium/wpcs_advance_settings_addon.php',
		'wpcs-display-multiple-sliders-premium/wpcs_display_multiple_sliders.php',
		'wpcs-fontawesome-icon-addon-premium/wpcs_fontawesome_icon_addon.php',
		'wpcs-triggers-addon-premium/wpcs_triggers_addon.php',
	);
	$addon_active = false;
	foreach ( $addons as $addon ) {
		if ( is_plugin_active( $addon ) ) {
			$addon_active = true;
			break;
		}
	}

	if ( ! $addon_active && $screen && 'post' === $screen->base ) {
		wp_add_inline_style(
			'wpcs-admin',
			'#post-body.columns-2 #side-sortables{min-height:1px;}div#postbox-container-1{display:flex;flex-direction:column-reverse;}'
		);
	}

}

add_action( 'admin_enqueue_scripts', 'wpcs_enque_backend_styles' );
