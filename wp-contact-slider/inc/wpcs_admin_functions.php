<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @author Mohammad Mursaleen
 * function to Register Custom Post Type for contact slider
 */
if ( ! function_exists( 'wpcs_post_type' ) ) {

	function wpcs_post_type() {

		$labels = array(

			'name'               => _x( 'WP Contact Sliders', 'Post Type General Name', 'wp-contact-slider' ),

			'singular_name'      => _x( 'WP Contact Slider', 'Post Type Singular Name', 'wp-contact-slider' ),

			'menu_name'          => __( 'WP Contact Slider', 'wp-contact-slider' ),

			'parent_item_colon'  => '',

			'all_items'          => __( 'All Sliders', 'wp-contact-slider' ),

			'view_item'          => __( 'View Contact Slider', 'wp-contact-slider' ),

			'add_new_item'       => __( 'Add New Contact Slider', 'wp-contact-slider' ),

			'add_new'            => __( 'Add New Contact Slider', 'wp-contact-slider' ),

			'edit_item'          => __( 'Edit Contact Slider', 'wp-contact-slider' ),

			'update_item'        => __( 'Update Contact Slider', 'wp-contact-slider' ),

			'search_items'       => __( 'Search Contact Slider', 'wp-contact-slider' ),

			'not_found'          => __( 'Not found', 'wp-contact-slider' ),

			'not_found_in_trash' => __( 'Not found in Trash', 'wp-contact-slider' ),

		);

		$args = array(

			'label'               => __( 'wpcs_post_type', 'wp-contact-slider' ),

			'description'         => __( 'For WP Contact Slider', 'wp-contact-slider' ),

			'labels'              => $labels,

			'supports'            => array( 'title' ),

			'hierarchical'        => true,

			'public'              => false,

			'show_ui'             => true,

			'show_in_menu'        => true,

			'show_in_nav_menus'   => true,

			'menu_icon'           => plugins_url( 'img/icon2.png', dirname( __FILE__ ) ),

			'show_in_admin_bar'   => true,

			'menu_position'       => 10,

			'can_export'          => true,

			'has_archive'         => false,

			'exclude_from_search' => true,

			'publicly_queryable'  => false,

			'capability_type'     => 'page',

			'menu_position'       => 10,

		);

		register_post_type( 'wpcs', $args );

	}

	// Hook into the 'init' action

	add_action( 'init', 'wpcs_post_type', 0 );

}

add_filter( 'manage_wpcs_posts_columns', 'wpcs_add_display_settings_column' );
/**
 * Adds Display settings column to the sliders list table.
 *
 * @param array $columns List table columns.
 * @return array
 */
function wpcs_add_display_settings_column( $columns ) {
	$new_columns = array();
	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;
		if ( 'title' === $key ) {
			$new_columns['wpcs_display_settings'] = __( 'Display settings', 'wp-contact-slider' );
		}
	}
	return $new_columns;
}

add_action( 'manage_wpcs_posts_custom_column', 'wpcs_render_display_settings_column', 10, 2 );
/**
 * Renders display rules for each slider in the list table.
 *
 * @param string $column  Column key.
 * @param int    $post_id Slider post ID.
 */
function wpcs_render_display_settings_column( $column, $post_id ) {
	if ( 'wpcs_display_settings' !== $column ) {
		return;
	}

	$badges = wpcs_get_display_settings_badges( $post_id );

	if ( empty( $badges ) ) {
		echo '&mdash;';
		return;
	}

	echo wp_kses(
		'<div class="wpcs-display-badges">' . implode( '', $badges ) . '</div>',
		array(
			'div'  => array(
				'class' => true,
			),
			'span' => array(
				'class' => true,
			),
		)
	);
}

/**
 * Builds HTML badge spans for display settings column.
 *
 * @param int $post_id Slider post ID.
 * @return string[] Badge HTML fragments.
 */
function wpcs_get_display_settings_badges( $post_id ) {
	$badges = array();

	if ( wpcs_is_display_meta_enabled( $post_id, 'wpcs_display_on_all' ) ) {
		$badges[] = wpcs_display_settings_badge_html( __( 'All pages', 'wp-contact-slider' ) );
	}

	if ( wpcs_is_display_meta_enabled( $post_id, 'wpcs_display_on_home' ) ) {
		$badges[] = wpcs_display_settings_badge_html( __( 'Homepage', 'wp-contact-slider' ) );
	}

	$page_ids = get_post_meta( $post_id, 'wpcs_pages', false );
	if ( ! empty( $page_ids[0] ) && is_array( $page_ids[0] ) ) {
		$page_ids = array_map( 'absint', $page_ids[0] );
		$max_show = 5;
		$shown    = 0;
		foreach ( $page_ids as $page_id ) {
			if ( $shown >= $max_show ) {
				break;
			}
			$title = get_the_title( $page_id );
			if ( $title ) {
				$badges[] = wpcs_display_settings_badge_html( $title );
				++$shown;
			}
		}
		if ( count( $page_ids ) > $max_show ) {
			$badges[] = wpcs_display_settings_badge_html(
				/* translators: %d: number of additional pages not shown in the list. */
				sprintf( __( '+%d more', 'wp-contact-slider' ), count( $page_ids ) - $max_show ),
				'more'
			);
		}
	}

	return $badges;
}

/**
 * Single display-settings badge markup.
 *
 * @param string $label    Badge label.
 * @param string $modifier Optional BEM modifier (e.g. more).
 * @return string
 */
function wpcs_display_settings_badge_html( $label, $modifier = '' ) {
	$class = 'wpcs-display-badge';
	if ( $modifier ) {
		$class .= ' wpcs-display-badge--' . sanitize_html_class( $modifier );
	}
	return '<span class="' . esc_attr( $class ) . '">' . esc_html( $label ) . '</span>';
}

/**
 * Whether a checkbox_list-style display meta is enabled.
 *
 * @param int    $post_id Post ID.
 * @param string $meta_key Meta key.
 * @return bool
 */
function wpcs_is_display_meta_enabled( $post_id, $meta_key ) {
	$value = get_post_meta( $post_id, $meta_key, true );
	if ( is_array( $value ) ) {
		return in_array( 'yes', $value, true );
	}
	return 'yes' === $value;
}


add_filter( 'get_sample_permalink_html', 'wpcs_remove_permalink_meta', '', 4 );
/**
 * @author Mohammad Mursaleen
 * function to remove permalink meta
 */
function wpcs_remove_permalink_meta( $return, $id, $new_title, $new_slug ) {
	global $post;
	if ( ! empty( $post ) && is_object( $post ) ) {
		if ( 'wpcs' === $post->post_type ) {
			$ret2 = '';
			return $ret2;
		} else {
			return $return;
		}
	}
}


add_filter( 'pre_get_shortlink', 'wpcs_remove_shortlink_button', 10, 2 );
/**
 * @author Mohammad Mursaleen
 * function to remove get short link button
 */
function wpcs_remove_shortlink_button( $false, $post_id ) {

	return 'wpcs' === get_post_type( $post_id ) ? '' : $false;

}


add_action( 'admin_enqueue_scripts', 'wpcs_enqueue_editor_script' );
/**
 * Editor script for the slider post screen.
 */
function wpcs_enqueue_editor_script() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'wpcs' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}

	wp_enqueue_script( 'jquery' );
	wp_add_inline_script( 'jquery', wpcs_get_editor_script() );
}

/**
 * JavaScript that toggles slider content meta boxes.
 *
 * @return string
 */
function wpcs_get_editor_script() {
	return 'jQuery(document).ready(function($){$(".rwmb-add-file").hide();$("#shortcode,#sales-reprensentatives,#text").hide();var value=$("input[name=wpcs_option]:checked","#post").val();if(value==="shortcode"){$("#text").hide();$("#shortcode").show();}if(value==="html"){$("#shortcode").hide();$("#text").show();}$("#post input").on("change",function(){var selected=$("input[name=wpcs_option]:checked","#post").val();if(selected==="shortcode"){$("#text").fadeOut(200);$("#shortcode").fadeIn(900);}if(selected==="html"){$("#shortcode").fadeOut(200);$("#text").fadeIn(900);}});});';
}

add_filter( 'gettext', 'wpcs_change_publish_text', 10, 2 );
/**
 * Change the publish button label on slider screens.
 *
 * @param string $translation Translated text.
 * @param string $text        Original text.
 * @return string
 */
function wpcs_change_publish_text( $translation, $text ) {
	if ( 'Publish' !== $text ) {
		return $translation;
	}
	if ( function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();
		if ( $screen && isset( $screen->post_type ) && 'wpcs' === $screen->post_type ) {
			return __( 'Save', 'wp-contact-slider' );
		}
	}
	return $translation;
}


add_action( 'do_meta_boxes', 'wpcs_remove_revolution_slider_meta_boxes' );
/**
 * @author Mohammad Mursaleen
 * function to avoid confliction with Revolution slider
 */
function wpcs_remove_revolution_slider_meta_boxes() {
	remove_meta_box( 'mymetabox_revslider_0', 'wpcs', 'normal' );
}

add_action( 'admin_notices', 'wpcs_old_addon_extension_notice' );
/**
 * Ask for an add-on update when an installed add-on still expects removed premium renderers.
 */
function wpcs_old_addon_extension_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$outdated = array();
	$addons   = array(
		'advanced-settings-wp-contact-slider/wpcs_advance_settings_addon.php' => 'WPCS_AS_EXTENSION_API',
		'font-awesome-icons-wp-contact-slider/wpcs_fontawesome_icon_addon.php' => 'WPCS_FAI_EXTENSION_API',
	);

	foreach ( $addons as $plugin => $constant ) {
		if ( is_plugin_active( $plugin ) && ! defined( $constant ) ) {
			$outdated[] = $plugin;
		}
	}

	if ( empty( $outdated ) ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'A WP Contact Slider add-on is active but needs an update for this plugin version. Slider settings are unchanged. Update the add-on to restore its extra display options.', 'wp-contact-slider' );
	echo '</p></div>';
}


