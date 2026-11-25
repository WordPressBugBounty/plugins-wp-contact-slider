<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_footer', 'wpcs_display_slider' );
/**
 * @author Mohammad Mursaleen
 * function to display WP Contact Slider on front end
 */
function wpcs_display_slider() {

	$slider_ids = wpcs_get_slider_ids_for_current_request();
	wpcs_get_tab_stack_layout( $slider_ids );

	foreach ( $slider_ids as $slider_id ) {
		wpcs_create_slider_slider( $slider_id );
	}
}

/**
 * Per-side tab counts and shared tab width (scaled when multiple sliders share a side).
 *
 * @param int[]|null $slider_ids Optional. Defaults to sliders for the current request.
 * @return array{left: array{count: int, tab_width: int}, right: array{count: int, tab_width: int}}
 */
function wpcs_get_tab_stack_layout( $slider_ids = null ) {
	static $layout = null;

	if ( null !== $layout ) {
		return $layout;
	}

	if ( null === $slider_ids ) {
		$slider_ids = wpcs_get_slider_ids_for_current_request();
	}

	$layout = array(
		'left'  => array(
			'count'     => 0,
			'tab_width' => 170,
		),
		'right' => array(
			'count'     => 0,
			'tab_width' => 170,
		),
	);

	$bases = array(
		'left'  => array(),
		'right' => array(),
	);

	foreach ( $slider_ids as $slider_id ) {
		$slider_id = (int) $slider_id;
		if ( ! apply_filters( 'wpcs_render_slider', true, $slider_id ) ) {
			continue;
		}

		$position = get_post_meta( $slider_id, 'wpcs_slider_position', true );
		$side     = ( 'left' === $position ) ? 'left' : 'right';

		$layout[ $side ]['count']++;
		$bases[ $side ][] = 170;
	}

	foreach ( array( 'left', 'right' ) as $side ) {
		if ( $layout[ $side ]['count'] < 1 ) {
			continue;
		}

		$base = ! empty( $bases[ $side ] ) ? max( $bases[ $side ] ) : 170;
		$layout[ $side ]['tab_width'] = wpcs_compute_side_tab_width( $layout[ $side ]['count'], $base, $side );
	}

	/**
	 * Add-ons may adjust free stacking metrics. They own any custom tab width.
	 */
	$layout = apply_filters( 'wpcs_tab_stack_layout', $layout, $slider_ids );

	return $layout;
}

/**
 * Tab width for a side: full base when alone, scaled down to fit the viewport when stacked.
 *
 * @param int    $count Sliders on this side.
 * @param int    $base  Largest configured tab width on this side.
 * @param string $side  left or right.
 * @return int
 */
function wpcs_compute_side_tab_width( $count, $base, $side ) {
	$count = (int) $count;
	$base  = max( 1, (int) $base );

	if ( $count <= 1 ) {
		return $base;
	}

	$stack_base = (int) apply_filters( 'wpcs_tab_stack_base', 200, null, $side );
	$usable     = (int) apply_filters( 'wpcs_tab_stack_usable_height', 680, null, $side );
	$gap        = (int) apply_filters( 'wpcs_tab_stack_gap', 12, null, $side );
	$min_width  = (int) apply_filters( 'wpcs_tab_width_min', 60, null, $side );

	$space  = max( 50, $usable - $stack_base );
	$scaled = (int) floor( ( $space - ( $count - 1 ) * $gap ) / $count );
	$scaled = min( $base, $scaled );
	$scaled = max( $min_width, $scaled );

	return (int) apply_filters( 'wpcs_tab_width_scaled', $scaled, $count, $base, $side );
}

/**
 * Published slider IDs that should render on the current front-end request (deduped).
 *
 * @return int[]
 */
function wpcs_get_slider_ids_for_current_request() {
	$current_page_id = wpcs_get_current_queried_content_id();

	$slider_ids = get_posts(
		array(
			'post_type'      => 'wpcs',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'ASC',
		)
	);

	if ( empty( $slider_ids ) ) {
		return array();
	}

	$matched = array();
	foreach ( $slider_ids as $slider_id ) {
		$slider_id = (int) $slider_id;
		if ( wpcs_should_display_slider( $slider_id, $current_page_id ) ) {
			$matched[] = $slider_id;
		}
	}

	return $matched;
}

/**
 * Current singular content ID for display rules (0 when not applicable).
 *
 * @return int
 */
function wpcs_get_current_queried_content_id() {
	global $wp_query;

	$post_obj = $wp_query->get_queried_object();
	if ( $post_obj && ! $post_obj instanceof WP_Post_Type && isset( $post_obj->ID ) ) {
		return (int) $post_obj->ID;
	}

	return 0;
}

/**
 * Whether a slider should appear on the current request.
 *
 * @param int $slider_id       Slider post ID.
 * @param int $current_page_id Current content ID (0 if none).
 * @return bool
 */
function wpcs_should_display_slider( $slider_id, $current_page_id ) {
	if ( wpcs_is_display_meta_enabled( $slider_id, 'wpcs_display_on_all' ) ) {
		return true;
	}

	if ( ( is_home() || is_front_page() ) && wpcs_is_display_meta_enabled( $slider_id, 'wpcs_display_on_home' ) ) {
		return true;
	}

	if ( $current_page_id > 0 && wpcs_slider_is_assigned_to_page( $slider_id, $current_page_id ) ) {
		return true;
	}

	return false;
}

/**
 * Normalized post/page IDs assigned to a slider (selected pages field).
 *
 * @param int $slider_id Slider post ID.
 * @return int[]
 */
function wpcs_get_slider_assigned_page_ids( $slider_id ) {
	$raw = get_post_meta( $slider_id, 'wpcs_pages', false );
	if ( ! is_array( $raw ) || empty( $raw ) ) {
		return array();
	}

	$page_ids = array();
	foreach ( $raw as $entry ) {
		if ( is_array( $entry ) ) {
			foreach ( $entry as $page_id ) {
				if ( is_numeric( $page_id ) ) {
					$page_ids[] = absint( $page_id );
				}
			}
		} elseif ( is_numeric( $entry ) ) {
			$page_ids[] = absint( $entry );
		}
	}

	$page_ids = array_filter( $page_ids );
	return array_values( array_unique( $page_ids ) );
}

/**
 * Whether the slider is assigned to display on a specific post or page.
 *
 * @param int $slider_id Slider post ID.
 * @param int $page_id     Post or page ID.
 * @return bool
 */
function wpcs_slider_is_assigned_to_page( $slider_id, $page_id ) {
	$page_id = absint( $page_id );
	if ( $page_id <= 0 ) {
		return false;
	}

	return in_array( $page_id, wpcs_get_slider_assigned_page_ids( $slider_id ), true );
}

/**
 * @param string|int $current_page_id Legacy callers may pass string IDs.
 */
function wpcs_check_sliders_for_this_page( $current_page_id ) {

	$page_id = absint( $current_page_id );
	if ( $page_id <= 0 ) {
		return;
	}

	$slider_ids = get_posts(
		array(
			'post_type'      => 'wpcs',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'ASC',
		)
	);

	foreach ( $slider_ids as $slider_id ) {
		if ( wpcs_slider_is_assigned_to_page( (int) $slider_id, $page_id ) ) {
			wpcs_create_slider_slider( (int) $slider_id );
		}
	}
}


/**
 * @author Mohammad Murasleen
 * @usage loop to call slider
 * @param $args
 */
function wpcs_slider_section( $args ) {

	$wpcs_query = new WP_Query( $args );

	while ( $wpcs_query->have_posts() ) {

		$wpcs_query->the_post();

		wpcs_create_slider_slider( get_the_id() );
	}

	wp_reset_postdata();

}

/**
 * @author Mohammad Mursaleen
 * @usage function to display slider content
 */
function wpcs_display_slider_content( $slider_id ) {

	// Check which option is selected to display in slider
	$wpcs_option = get_post_meta( $slider_id, 'wpcs_option', true );

	switch ( $wpcs_option ) {

		case 'html':
			$wpcs_html = get_post_meta( $slider_id, 'wpcs_html', true );
			// check if the custom field has a value
			if ( ! empty( $wpcs_html ) ) {
				$wpcs_html = apply_filters( 'wpcs_html_content', $wpcs_html, $slider_id );
                echo wp_kses( $wpcs_html, wpcs_allowed_html() );

			}
			break;

		case 'shortcode':
			$wpcs_shortcode   = get_post_meta( $slider_id, 'wpcs_shortcode', true );
			$wpcs_plugin_name = get_post_meta( $slider_id, 'wpcs_plugin_name', true );

			// check if the custom field has a value
			if ( ! empty( $wpcs_shortcode ) ) {

				switch ( $wpcs_plugin_name ) {

					case 'cf7':
						?>
						<div class="wpcs-cf7">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'gf':
						?>
						<div class="wpcs-gf">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'wp-form':
						?>
						<div class="wpcs-wp-form">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'caldera-form':
						?>
						<div class="wpcs-caldera-form">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							wpcs_add_front_inline_style( '.cfdatepicker-dropdown {z-index: 99999999999 !important;}' );
							wpcs_add_front_inline_script( 'jQuery(".cfdatepicker").click(function(){ setTimeout(function(){ jQuery(".cfdatepicker-dropdown").each(function(){ this.style.setProperty("z-index","99999999999","important"); }); }, 100); });' );
							?>
						</div>
						<?php

						break;

					case 'constant-forms':
						?>
						<div class="wpcs-constant-forms">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'pirate-forms':
						?>
						<div class="wpcs-pirate-forms">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'si-contact-form':
						?>
						<div class="wpcs-si-contact-form">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'formidable':
						?>
						<div class="wpcs-formidable">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'form-maker':
						?>
						<div class="wpcs-form-maker">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'form-craft':
						?>
						<div class="wpcs-form-craft">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'ninja-forms':
						?>
						<div class="wpcs-ninja-forms">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							wpcs_add_front_inline_style( '.pika-single {z-index: 99999999999 !important;}' );
							?>
						</div>
						<?php
						break;

					case 'visual-form-builder':
						?>
						<div class="wpcs-visual-form-builder">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					case 'mycred':
						?>
						<div class="wpcs-mycred">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

					default:
						?>
						<div class="wpcs-cf7">
							<?php
							echo do_shortcode( $wpcs_shortcode );
							?>
						</div>
						<?php
						break;

				}
			}
			break;

		default:
			echo esc_html__( 'Kindly select some option in your slider to display here.', 'wp-contact-slider' );
	}

}





















