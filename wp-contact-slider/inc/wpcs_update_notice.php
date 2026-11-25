<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_enqueue_scripts', 'wpcs_enqueue_notice_assets' );
/**
 * Notice CSS and dismiss script, only on slider screens.
 *
 * @param string $hook_suffix Current admin page.
 */
function wpcs_enqueue_notice_assets( $hook_suffix ) {
	unset( $hook_suffix );

	if ( ! wpcs_is_wpcs_admin_screen() || ! wpcs_time_to_display_update_notice() ) {
		return;
	}

	$version = defined( 'WPCS_VERSION' ) ? WPCS_VERSION : '2.5.6';
	$base    = dirname( __DIR__ ) . '/wp_contact_slider.php';

	wp_enqueue_style( 'wpcs-admin-notice', plugins_url( 'css/admin-notice.css', $base ), array(), $version );
	wp_enqueue_script( 'jquery' );
	wp_add_inline_script(
		'jquery',
		'jQuery(document).on("click", ".wpcs-first-notice .notice-dismiss", function(){ jQuery.ajax({ url: ajaxurl, type: "POST", data: { action: "dismiss_wpcs_notice", nonce: "' . esc_js( wp_create_nonce( 'wpcs_dismiss_notice' ) ) . '" } }); });'
	);
}

/**
 * Whether the current admin screen belongs to the slider post type.
 *
 * @return bool
 */
function wpcs_is_wpcs_admin_screen() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}
	$screen = get_current_screen();
	return $screen && isset( $screen->post_type ) && 'wpcs' === $screen->post_type;
}

add_action( 'admin_notices', 'wpcs_update_notice' );
/**
 * Display the add-ons notice on slider screens.
 */
function wpcs_update_notice() {
	if ( ! wpcs_time_to_display_update_notice() || ! wpcs_is_wpcs_admin_screen() ) {
		return;
	}

	$page = filter_input( INPUT_GET, 'page', FILTER_CALLBACK, array( 'options' => 'sanitize_key' ) );
	if ( 'wp-contact-slider-addons' === $page ) {
		update_option( 'wpcs_display_notice_2_2', 'no' );
		return;
	}

	$addons_url = 'https://wpcontactslider.com/addons-bundle/';
	$base       = dirname( __DIR__ ) . '/wp_contact_slider.php';
	$images     = array(
		'notice-images/bundle-addon-icon.png',
		'notice-images/advance-setting-icon.png',
		'notice-images/trigers-and-shortcodes.png',
		'notice-images/font-awesome-icon.png',
		'notice-images/multiple-sliders.png',
	);
	$urls       = array();
	foreach ( $images as $image ) {
		$urls[] = esc_url( plugins_url( 'img/' . $image, $base ) );
	}

	$heading = esc_html__( 'GET ADD-ONS BUNDLE FOR WP CONTACT SLIDER', 'wp-contact-slider' );
	$intro   = esc_html__( 'Unleash the power of WP Contact Slider with AddOns Bundle', 'wp-contact-slider' );
	$coupon  = sprintf(
		/* translators: %s: coupon code. */
		esc_html__( 'Use Coupon code %s to get 40%% discount on LifeTime Bundles', 'wp-contact-slider' ),
		'<span class="wpcs_coupon_code">LIFETIME40FF</span>'
	);
	$button  = esc_html__( 'GET ADD-ONS BUNDLE', 'wp-contact-slider' );
	$link    = esc_url( $addons_url );
	?>
	<div data-dismissible="notice-one-forever-wpcs" class="notice notice-info is-dismissible wpcs-first-notice">
		<h2><?php echo esc_html( $heading ); ?></h2>
		<div class="wp_contact_notice">
			<div class="wp-6-c">
				<div class="logo-contact"><a href="<?php echo esc_url( $link ); ?>"><img alt="" src="<?php echo esc_url( $urls[0] ); ?>"></a></div>
				<div class="content-contact">
					<h2><?php echo esc_html( $heading ); ?></h2>
					<p><?php echo esc_html( $intro ); ?></p>
					<p class="wpcs_coupon_line_wrap"><span class="wpcs_coupon_line"><?php echo wp_kses( $coupon, array( 'span' => array( 'class' => true ) ) ); ?></span></p>
					<a href="<?php echo esc_url( $link ); ?>" class="button button-hero button-primary"><?php echo esc_html( $button ); ?></a>
				</div>
			</div>
			<div class="wp-6-c">
				<a href="<?php echo esc_url( $link ); ?>"><img alt="" src="<?php echo esc_url( $urls[1] ); ?>"></a>
				<a href="<?php echo esc_url( $link ); ?>"><img alt="" src="<?php echo esc_url( $urls[2] ); ?>"></a>
				<a href="<?php echo esc_url( $link ); ?>"><img alt="" src="<?php echo esc_url( $urls[3] ); ?>"></a>
				<a href="<?php echo esc_url( $link ); ?>"><img alt="" src="<?php echo esc_url( $urls[4] ); ?>"></a>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Save the option that hides the notice.
 */
function wpcs_dismiss_update_notice() {
	check_ajax_referer( 'wpcs_dismiss_notice', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'Forbidden' ), 403 );
	}

	update_option( 'wpcs_display_notice_2_2', 'no' );
	wp_send_json_success();
}
add_action( 'wp_ajax_dismiss_wpcs_notice', 'wpcs_dismiss_update_notice' );

/**
 * Whether the delayed admin notice should show.
 *
 * @return bool
 */
function wpcs_time_to_display_update_notice() {
	if ( get_transient( 'wpcs-admin-notice-2-2-hold' ) ) {
		return false;
	}

	if ( get_option( 'wpcs_display_notice_2_2' ) === 'no' ) {
		return false;
	}

	return true;
}
