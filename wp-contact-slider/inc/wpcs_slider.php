<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether this free release exposes generic slider extension hooks.
 *
 * Add-ons use this to decide between legacy filters and their own CSS/JS.
 *
 * @return bool
 */
function wpcs_supports_slider_extensions() {
	return true;
}

/**
 * Builds a safe background-image declaration for inline slider CSS.
 *
 * Accepts a plain URL (current add-ons) or legacy full `background-image: url(...)` strings.
 *
 * @param string $bg_image URL or legacy CSS fragment.
 * @return string CSS declaration or empty string.
 */
function wpcs_slider_background_image_css( $bg_image ) {
	if ( ! is_string( $bg_image ) || '' === trim( $bg_image ) ) {
		return '';
	}

	$bg_image = trim( $bg_image );

	if ( preg_match( '#^https?://#i', $bg_image ) ) {
		return 'background-image: url(' . esc_url( $bg_image ) . ');';
	}

	if ( preg_match( '#background-image\s*:\s*url\s*\(\s*[\'"]?([^\'"\)]+)#i', $bg_image, $matches ) ) {
		return 'background-image: url(' . esc_url( trim( $matches[1] ) ) . ');';
	}

	return '';
}

/**
 * Keep a CSS size token (number or number plus a unit).
 *
 * @param mixed  $value   Raw value.
 * @param string $default Fallback.
 * @return string
 */
function wpcs_css_size( $value, $default = '0' ) {
	$value = is_scalar( $value ) ? trim( (string) $value ) : '';
	if ( preg_match( '/^[0-9.]+(%|px|em|rem|vh|vw)?$/', $value ) ) {
		return $value;
	}
	return $default;
}

/**
 * Keep a hex color, or empty when the saved value is not a color.
 *
 * @param mixed $color Raw color.
 * @return string
 */
function wpcs_css_color( $color ) {
	$color = is_string( $color ) ? trim( $color ) : '';
	$hex   = sanitize_hex_color( $color );
	return $hex ? $hex : '';
}

/**
 * Register footer handles used for per-slider inline CSS and JS.
 */
function wpcs_register_front_assets() {
	$version = defined( 'WPCS_VERSION' ) ? WPCS_VERSION : '2.5.6';
	$base    = dirname( __DIR__ ) . '/wp_contact_slider.php';

	if ( ! wp_style_is( 'wpcs-front', 'registered' ) ) {
		wp_register_style( 'wpcs-front', plugins_url( 'css/wpcs-front.css', $base ), array(), $version );
	}
	if ( ! wp_script_is( 'wpcs-front', 'registered' ) ) {
		wp_register_script( 'wpcs-front', plugins_url( 'js/wpcs-front.js', $base ), array( 'jquery' ), $version, true );
	}

	wp_enqueue_style( 'wpcs-front' );
	wp_enqueue_script( 'wpcs-front' );
}

/**
 * Append CSS that prints with the late footer styles.
 *
 * @param string $css CSS rules.
 */
function wpcs_add_front_inline_style( $css ) {
	$css = trim( (string) $css );
	if ( '' === $css ) {
		return;
	}
	wpcs_register_front_assets();
	wp_add_inline_style( 'wpcs-front', $css );
}

/**
 * Append JS that prints with the footer script.
 *
 * @param string $js JavaScript.
 */
function wpcs_add_front_inline_script( $js ) {
	$js = trim( (string) $js );
	if ( '' === $js ) {
		return;
	}
	wpcs_register_front_assets();
	wp_add_inline_script( 'wpcs-front', $js );
}

/**
 * function to display slider
 **/
function wpcs_create_slider_slider( $slider_id ) {

	if ( ! apply_filters( 'wpcs_render_slider', true, $slider_id ) ) { // slider will be displaye only if the value of this filter is returned true
		return;
	}

	$prefix = '_' . $slider_id;

	$title               = apply_filters( 'wpcs_tab_title', get_the_title( $slider_id ), $slider_id );
	$label_text_color    = get_post_meta( $slider_id, 'wpcs_lable_text_color', true );
	$label_bg_color      = get_post_meta( $slider_id, 'wpcs_lable_bg_color', true );
	$label_border_color  = get_post_meta( $slider_id, 'wpcs_lable_border_color', true );
	$slider_position     = get_post_meta( $slider_id, 'wpcs_slider_position', true );
	$slider_text_color   = get_post_meta( $slider_id, 'wpcs_slider_text_color', true );
	$slider_bg_color     = get_post_meta( $slider_id, 'wpcs_slider_bg_color', true );
	$form_bg_color       = get_post_meta( $slider_id, 'wpcs_form_bg_color', true );
	$slider_border_color = get_post_meta( $slider_id, 'wpcs_slider_border_color', true );
	$hide_on_mobile      = get_post_meta( $slider_id, 'wpcs_hide_on_mobile', true );
	$open_form           = get_post_meta( $slider_id, 'wpcs_open_form', true );
	$scroll_bar          = get_post_meta( $slider_id, 'wpcs_enable_form_scroll', true );
	$position            = ! empty( $slider_position ) ? $slider_position : 'right';

	$open_on_page_load       = ! empty( $open_form ) ? $open_form : 'no';
	$open_on_page_load_after = 2 * 1000;
	$amimation_speed         = 250;

	$width            = 500;
	$container_height = '100%';
	$side             = ( 'left' === $position ) ? 'left' : 'right';
	$base_tab_width   = 170;
	$tab_layout     = wpcs_get_tab_stack_layout();
	$side_count     = isset( $tab_layout[ $side ]['count'] ) ? (int) $tab_layout[ $side ]['count'] : 0;
	$stacked        = $side_count > 1;

	if ( $stacked ) {
		$tab_width            = (int) $tab_layout[ $side ]['tab_width'];
		$side_position        = 0;
		$tab_transform_origin = ( 'left' === $position ) ? 'left top' : 'right top';
		$tab_font_size        = $tab_width < 110 ? 14 : ( $tab_width < 140 ? 16 : 18 );
	} else {
		$tab_width            = $base_tab_width;
		$side_position        = 68;
		$tab_transform_origin = '50% 50%';
		$tab_font_size        = 18;
	}

	static $wpcs_tab_stack_top = array( 'left' => 200, 'right' => 200 );
	$stack_gap = (int) apply_filters( 'wpcs_tab_stack_gap', 12, $slider_id, $side );
	$top       = $wpcs_tab_stack_top[ $side ];
	$wpcs_tab_stack_top[ $side ] += $tab_width + $stack_gap;
	$tab_classes = apply_filters( 'wpcs_tab_classes', '', $slider_id );

	$cross_icon_src   = plugins_url( 'img/delete-sign.png', dirname( __FILE__ ) );
	$cursor_close_src = plugins_url( 'img/cursor_close.png', dirname( __FILE__ ) );
	$slider_js_id     = preg_replace( '/[^A-Za-z0-9_]/', '', (string) $prefix );
	$slider_side      = ( 'left' === $position ) ? 'left' : 'right';
	$slider_width = (int) $width;
	$slider_speed = (int) $amimation_speed;
	$slider_delay = (int) $open_on_page_load_after;
	$open_js      = ( 'yes' === $open_on_page_load ) ? 'setTimeout( function(){ wpcs_open_slider' . $slider_js_id . '(); }, ' . $slider_delay . ' );' : '';

	ob_start();
	do_action( 'wpcs_slider_scripts', $slider_id );
	$extra_js = ob_get_clean();

	wpcs_add_front_inline_script(
		'jQuery(document).ready(function($){
			jQuery("#wpcs_tab' . $slider_js_id . '").click(function($){
				if( ! (jQuery("#wpcs_content_main' . $slider_js_id . '").hasClass("is_open")) ){
					wpcs_open_slider' . $slider_js_id . '();
				} else {
					wpcs_close_slider' . $slider_js_id . '();
				}
			});
			jQuery("#wpcs_overlay' . $slider_js_id . ', #wpcs_close_slider' . $slider_js_id . '").click(function(){
				wpcs_close_slider' . $slider_js_id . '();
			});
			' . $open_js . '
		});
		function wpcs_open_slider' . $slider_js_id . '(do_repeat){
			do_repeat = typeof do_repeat !== "undefined" ? do_repeat : 0 ;
			if( do_repeat !== 0 ){
				jQuery("#wpcs_content_main' . $slider_js_id . '").addClass("do_repeat");
				jQuery( "#wpcs_content_main' . $slider_js_id . '" ).data( "interval", do_repeat );
			}
			if( ! (jQuery("#wpcs_content_main' . $slider_js_id . '").hasClass("is_open")) && !(jQuery("#wpcs_content_main' . $slider_js_id . '").hasClass("is_opening")) ){
				jQuery("#wpcs_tab' . $slider_js_id . ',.wpcs_tab").fadeTo("slow", 0);
				jQuery("#wpcs_content_main' . $slider_js_id . '").addClass("is_opening");
				jQuery("#wpcs_overlay' . $slider_js_id . '").addClass("wpcs_overlay_display_cross");
				jQuery( "#wpcs_overlay' . $slider_js_id . '").fadeIn("fast");
				jQuery("#wpcs_content_main' . $slider_js_id . '").addClass("is_open");
				jQuery( "#wpcs_content_main' . $slider_js_id . '" ).animate({
					opacity: 1,
					' . $slider_side . ': "+=' . $slider_width . '"
				}, ' . $slider_speed . ' , function() {
					jQuery("#wpcs_tab' . $slider_js_id . ',.wpcs_tab").fadeTo("slow", 0);
					jQuery( "#wpcs_content_inner' . $slider_js_id . '").fadeTo("slow" , 1);
					jQuery("#wpcs_content_main' . $slider_js_id . '").removeClass("is_opening");
				});
			}
		}
		function wpcs_close_slider' . $slider_js_id . '(){
			if( (jQuery("#wpcs_content_main' . $slider_js_id . '").hasClass("is_open")) && !(jQuery("#wpcs_content_main' . $slider_js_id . '").hasClass("is_closing")) ) {
				jQuery("#wpcs_overlay' . $slider_js_id . '").removeClass("wpcs_overlay_display_cross");
				jQuery("#wpcs_content_main' . $slider_js_id . '").addClass("is_closing");
				jQuery("#wpcs_content_main' . $slider_js_id . '").animate({
					' . $slider_side . ': "-=' . $slider_width . '"
				}, ' . $slider_speed . ' , function () {
					jQuery("#wpcs_content_main' . $slider_js_id . '").fadeTo("fast", 0);
					jQuery("#wpcs_content_inner' . $slider_js_id . '").slideUp("fast");
					jQuery("#wpcs_overlay' . $slider_js_id . '").fadeOut("slow");
					jQuery("body").removeClass("fixed-body");
					jQuery("#wpcs_content_main' . $slider_js_id . '").removeClass("is_open");
					jQuery("#wpcs_content_main' . $slider_js_id . '").removeClass("is_closing");
					jQuery("#wpcs_tab' . $slider_js_id . ',.wpcs_tab").fadeTo("slow", 1);
				});
			}
		}
		' . $extra_js
	);

	$label_text   = wpcs_css_color( $label_text_color );
	$label_bg     = wpcs_css_color( $label_bg_color );
	$label_border = wpcs_css_color( $label_border_color );
	$text_color   = wpcs_css_color( $slider_text_color );
	$panel_bg     = wpcs_css_color( $slider_bg_color );
	$form_bg      = wpcs_css_color( $form_bg_color );
	$panel_border = wpcs_css_color( $slider_border_color );
	$border_edge  = ( 'left' === $slider_side ) ? 'top' : 'bottom';
	$close_edge   = ( 'left' === $slider_side ) ? 'right' : 'left';
	$cursor_url   = esc_url( $cursor_close_src );
	$scroll_css   = ( 'yes' !== $scroll_bar ) ? '#wpcs_content_main' . $slider_js_id . '::-webkit-scrollbar { display: none; }' : '';
	$mobile_css   = ( 'yes' === $hide_on_mobile ) ? '@media (max-width: 600px) { #wpcs_tab' . $slider_js_id . ', #wpcs_content_main' . $slider_js_id . ', #wpcs_overlay' . $slider_js_id . ' { display: none !important; } }' : '';

	ob_start();
	do_action( 'wpcs_slider_style', $slider_id );
	$extra_css = ob_get_clean();

	wpcs_add_front_inline_style(
		'.fixed-body{ position: relative; ' . $slider_side . ': 0px; }
		div#wpcs_tab' . $slider_js_id . ' {
			border: 1px solid ' . $label_border . ';
			border-' . $border_edge . ':none;
			cursor: pointer;
			width: ' . (int) $tab_width . 'px;
			height: 34px;
			overflow: hidden;
			background: ' . $label_bg . ';
			color: ' . $label_text . ';
			padding: 2px 0px 2px 0px;
			position: fixed;
			top: ' . (int) $top . 'px;
			' . $slider_side . ': -' . (int) $side_position . 'px;
			text-align: center;
			-webkit-transform-origin: ' . $tab_transform_origin . ';
			transform-origin: ' . $tab_transform_origin . ';
			-webkit-transform: rotate(-90deg);
			transform: rotate(-90deg);
			z-index: 9999999;
			font-size: ' . (int) $tab_font_size . 'px;
		}
		div#wpcs_content_main' . $slider_js_id . ' {
			opacity:0;
			position: fixed;
			overflow-y: scroll;
			width: ' . wpcs_css_size( $width, '500' ) . 'px;
			max-width: 100%;
			height: ' . wpcs_css_size( $container_height, '100%' ) . ';
			background: ' . $panel_bg . ';
			color: black;
			top: 0px;
			' . $slider_side . ': -' . wpcs_css_size( $width, '500' ) . 'px;
			padding: 0px;
			margin: 0px;
			z-index: 9999999;
		}
		#wpcs_close_slider' . $slider_js_id . ' img { max-width: 100%; }
		div#wpcs_content_inner' . $slider_js_id . ' {
			display: none;
			max-width: 100%;
			min-height: 100%;
			background: ' . $form_bg . ';
			padding: 20px 20px 20px 20px;
			margin: 60px 40px 60px 40px;
			color: ' . $text_color . ';
			border: 1px solid ' . $panel_border . ';
		}
		div#wpcs_content_inner' . $slider_js_id . ' label{ color: ' . $text_color . '; }
		div#wpcs_overlay' . $slider_js_id . '{
			display: none;
			width: 100%;
			height: 100%;
			position: fixed;
			top: 0px;
			left: 0px;
			z-index: 999999;
			background: rgba(49, 49, 49, 0.65);
		}
		.wpcs_overlay_display_cross{ cursor: url(' . $cursor_url . '), auto; }
		' . $scroll_css . '
		div#wpcs_close_slider' . $slider_js_id . ' {
			top: 0px;
			' . $close_edge . ': 0px;
			position: absolute;
			bottom: 0px;
			width: 32px;
			height: 32px;
			cursor: pointer;
			background: #0000007a;
			padding: 0px;
			overflow: hidden;
		}
		.wpcs-cf7, .wpcs-gf, .wpcs-wp-form, .wpcs-caldera-form, .wpcs-constant-forms,
		.wpcs-pirate-forms, .wpcs-si-contact-form, .wpcs-formidable, .wpcs-form-maker, .wpcs-form-craft,
		.visual-form-builde { overflow: hidden; }
		' . $mobile_css . '
		' . $extra_css
	);

	do_action( 'wpcs_before_slider', $slider_id );
	$tab_html = esc_html( $title );
	$tab_html = apply_filters( 'wpcs_slider_tab_html', $tab_html, $slider_id );
	?>
	<!-- WP Contact Slider -- start -->
	<div id="wpcs_tab<?php echo esc_attr( $prefix ); ?>" class="wpcs_tab <?php echo esc_attr( $tab_classes ); ?>" aria-label="<?php echo esc_attr( $title ); ?>"><?php echo wp_kses( $tab_html, array( 'i' => array( 'class' => true ), 'span' => array( 'class' => true ) ) ); ?></div>
	<div id="wpcs_content_main<?php echo esc_attr( $prefix ); ?>" class="wpcs_content_main">
		<div id="wpcs_close_slider<?php echo esc_attr( $prefix ); ?>" aria-label="close slider" class="wpcs_close_slider_icon" ><img alt="close slider" src="<?php echo esc_attr( $cross_icon_src ); ?>"></div>
		<div id="wpcs_content_inner<?php echo esc_attr( $prefix ); ?>" class="wpcs_content_inner" >
			<?php do_action( 'wpcs_before_slider_content', $slider_id ); ?>
			<?php wpcs_display_slider_content( $slider_id ); ?>
			<?php do_action( 'wpcs_after_slider_content', $slider_id ); ?>
		</div>
	</div>
	<!-- WP Contact Slider -- end -->
	<?php do_action( 'wpcs_after_slider', $slider_id ); ?>
	<div id="wpcs_overlay<?php echo esc_attr( $prefix ); ?>"></div>
	<?php

}
