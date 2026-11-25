<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'rwmb_meta_boxes', 'wpcs_register_meta_boxes' );
/**
 * @author Mohammad Mursaleen
 * @description function to register and add Meta boxes for WP Contact Slider
 */
function wpcs_register_meta_boxes( $meta_boxes ) {
	$prefix = 'wpcs_';

	// 1st meta box for color scheme & Settings
	$meta_boxes[] = array(
		// Meta box id, UNIQUE per meta box. Optional since 4.1.5
		'id'       => 'standard',
		// Meta box title - Will appear at the drag and drop handle bar. Required.
		'title'    => __( 'Slider Settings', 'wp-contact-slider' ),
		// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
		'pages'    => array( 'wpcs' ),
		// Where the meta box appear: normal (default), advanced, side. Optional.
		'context'  => 'normal',
		// Order of meta box: high (default), low. Optional.
		'priority' => 'high',
		// Auto save: true, false (default). Optional.
		'autosave' => true,
		'clone'    => true,
		// List of meta fields
		'fields'   => array(

			// ******************************* Color scheme section header **************************
			array(
				'type' => 'heading',
				'name' => 'Color Settings',
				'desc' => 'Set colors as per your theme',
			),

			// Label text color
			array(
				// Field name - Will be used as label
				'name' => __( 'Label text color', 'wp-contact-slider' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}lable_text_color",
				// Field description (optional)
				'desc' => __( 'Choose color for your text of label', 'wp-contact-slider' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#ffffff', 'wp-contact-slider' ),

			),
			// Label background color
			array(
				// Field name - Will be used as label
				'name' => __( 'Label background color', 'wp-contact-slider' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}lable_bg_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the background of label', 'wp-contact-slider' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#0c0000', 'wp-contact-slider' ),

			),
			// Label border color
			array(
				// Field name - Will be used as label
				'name' => __( 'Label border color', 'wp-contact-slider' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}lable_border_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the border of Slider', 'wp-contact-slider' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#7f7f7f', 'wp-contact-slider' ),
			),
			// slider text color
			array(
				// Field name - Will be used as label
				'name' => __( 'Slider text color', 'wp-contact-slider' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}slider_text_color",
				// Field description (optional)
				'desc' => __( 'Choose color for your text of Slider', 'wp-contact-slider' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#0c0000', 'wp-contact-slider' ),

			),
			// slider background color
			array(
				// Field name - Will be used as label
				'name' => __( 'Slider background color', 'wp-contact-slider' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}slider_bg_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the background of Slider', 'wp-contact-slider' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#ffffff', 'wp-contact-slider' ),

			),
			// Form background color
			array(
				// Field name - Will be used as label
				'name' => __( 'Form background color', 'wp-contact-slider' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}form_bg_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the for the background of form container', 'wp-contact-slider' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#fcfcfc', 'wp-contact-slider' ),
			),
			// Form border color
			array(
				// Field name - Will be used as label
				'name' => __( 'Form border color', 'wp-contact-slider' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}slider_border_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the border of the form container', 'wp-contact-slider' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#0c0000', 'wp-contact-slider' ),
			),

			// ************** Display Settings section heading  *******************************
			array(
				'type' => 'custom_html',
				'std'  => '<br>',
			),
			array(
				'type' => 'heading',
				'name' => 'Display Settings',
				'desc' => 'Set where and how you would like to display this slider',
			),

			array(
				'type'    => 'select',
				'name'    => __( 'Slider position', 'wp-contact-slider' ),
				'id'      => "{$prefix}slider_position", // Not used but needed for plugin
				'desc'    => __( 'Select the position of contact slider', 'wp-contact-slider' ),
				'options' => array(
					'left'  => __( ' Left ', 'wp-contact-slider' ),
					'right' => __( ' Right ', 'wp-contact-slider' ),
				),

			),

			array(
				'name'    => __( 'Enable scroll bar', 'wp-contact-slider' ),
				'id'      => "{$prefix}enable_form_scroll",
				'class'   => "{$prefix}enable_form_scroll",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Enable scroll bar in slider window<br><strong>Note</strong>: This will be helpful if you would like to avoid mouse scroll only', 'wp-contact-slider' ),
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'yes' => '',
				),
			),

			array(
				'name'    => __( 'Hide on small devices', 'wp-contact-slider' ),
				'id'      => "{$prefix}hide_on_mobile",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Check this box if you would like to hide this slider on small devices less then 500px', 'wp-contact-slider' ),
				'options' => array(
					'yes' => '',
				),
			),

			array(
				'name'    => __( 'Open slider by default', 'wp-contact-slider' ),
				'id'      => "{$prefix}open_form",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Check this box if you would like to open this slider by default when page is loaded', 'wp-contact-slider' ),
				'options' => array(
					'yes' => '',
				),
			),

			array(
				'name'    => __( 'Display on Home', 'wp-contact-slider' ),
				'id'      => "{$prefix}display_on_home",
				'class'   => "{$prefix}display_on_home",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Check this box if you would like display this contact slider on Home Page', 'wp-contact-slider' ),
				'options' => array(
					'yes' => '',
				),
			),
			array(
				'name'    => __( 'Display on All Pages', 'wp-contact-slider' ),
				'id'      => "{$prefix}display_on_all",
				'class'   => "{$prefix}display_on_all",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Check this box to show this slider on every page. Other sliders that also match the current page can appear at the same time.', 'wp-contact-slider' ),
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'yes' => '',
				),
			),

			array(
				'name'       => __( 'Display on Selected Posts & Pages', 'wp-contact-slider' ),
				'id'         => "{$prefix}pages",
				'type'       => 'post',
				'clone'      => true,
				// Post type
				'post_type'  => array( 'post', 'page' ),
				'std'        => __( 'All', 'wp-contact-slider' ),
				// Field type, either 'select' or 'select_advanced' (default)
				'field_type' => 'select_advanced',
				'desc'       => __( 'Select posts and pages where you want to display this slider', 'wp-contact-slider' ),
				// Query arguments (optional). No settings means get all published posts
				'query_args' => array(
					'post_status' => 'publish',
				),
			),

			array(
				'name'    => __( 'What would you like to display?', 'wp-contact-slider' ),
				'id'      => "{$prefix}option",
				'type'    => 'radio',
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'shortcode' => __( 'Shortcode', 'wp-contact-slider' ),
					'html'      => __( 'Text or html', 'wp-contact-slider' ),
				),
			),

		),
	);

	// 2nd meta box for shortcode info
	$meta_boxes[] = array(
		'title'    => __( 'Shortcode', 'wp-contact-slider' ),
		'pages'    => array( 'wpcs' ),
		// Where the meta box appear: normal (default), advanced, side. Optional.
		'context'  => 'normal',
		// Order of meta box: high (default), low. Optional.
		'priority' => 'low',
		'fields'   => array(
			// shortcode
			array(
				'type'        => 'text',
				'name'        => __( 'Shortcode', 'wp-contact-slider' ),
				'id'          => "{$prefix}shortcode", // Not used but needed for plugin
				'desc'        => __( 'Enter your shortcode only if you want to use it in slider', 'wp-contact-slider' ),
				'placeholder' => __( 'Enter your short code', 'wp-contact-slider' ),

			),
			array(
				'type'    => 'select',
				'name'    => __( 'Type', 'wp-contact-slider' ),
				'id'      => "{$prefix}plugin_name", // Not used but needed for plugin
				'desc'    => __( 'Select the name of Plugin for which you want to use short code', 'wp-contact-slider' ),
				'options' => array(
					'none'                => __( 'none', 'wp-contact-slider' ),
					'gf'                  => __( 'Gravity Forms', 'wp-contact-slider' ),
					'cf7'                 => __( 'Contactform 7 ', 'wp-contact-slider' ),
					'wp-form'             => __( 'Wp-forms ', 'wp-contact-slider' ),
					'caldera-form'        => __( 'Caldera forms ', 'wp-contact-slider' ),
					'constant-forms'      => __( 'Constant Contact Forms ', 'wp-contact-slider' ),
					'pirate-forms'        => __( 'Pirate Forms ', 'wp-contact-slider' ),
					'si-contact-form'     => __( 'Fast Secure Contact Form', 'wp-contact-slider' ),
					'formidable'          => __( 'Formidable Forms', 'wp-contact-slider' ),
					'form-maker'          => __( 'Form Maker', 'wp-contact-slider' ),
					'form-craft'          => __( 'Form Craft', 'wp-contact-slider' ),
					'ninja-forms'         => __( 'Ninja Forms', 'wp-contact-slider' ),
					'visual-form-builder' => __( 'Visul Form Builder', 'wp-contact-slider' ),
					'mycred'              => __( 'Mycred', 'wp-contact-slider' ),
				),
			),
		),
	);

	// 3rd meta box for displaying text
	$meta_boxes[] = array(
		'title'    => __( 'Text', 'wp-contact-slider' ),
		'pages'    => array( 'wpcs' ),
		// Where the meta box appear: normal (default), advanced, side. Optional.
		'context'  => 'normal',
		// Order of meta box: high (default), low. Optional.
		'priority' => 'low',
		'fields'   => array(
			// shortcode
			array(
				'name'    => __( 'Text to display', 'wp-contact-slider' ),
				'id'      => "{$prefix}html", // Not used but needed for plugin
				'desc'    => __( 'Enter text or Html if you want to display', 'wp-contact-slider' ),
				'type'    => 'wysiwyg',
				// Set the 'raw' parameter to TRUE to prevent data being passed through wpautop() on save
				'raw'     => false,
				'std'     => '',
				// Editor settings, see wp_editor() function: look4wp.com/wp_editor
				'options' => array(
					'textarea_rows' => 15,
					'teeny'         => true,
					'media_buttons' => true,

				),
			),
		),
	);

	return apply_filters( 'wpcs_meta_boxes', $meta_boxes );

}




add_action( 'submitpost_box', 'wpcs_add_pro_banner' );	

function wpcs_add_pro_banner() {
	
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

    global $post_type;
	
    if ( 
		$post_type === 'wpcs' 
		&& 
		( 
		! is_plugin_active( 'wpcs-advance-settings-addon-premium/wpcs_advance_settings_addon.php' ) 
		&& ! is_plugin_active( 'wpcs-display-multiple-sliders-premium/wpcs_display_multiple_sliders.php' ) 
		&& ! is_plugin_active( 'wpcs-fontawesome-icon-addon-premium/wpcs_fontawesome_icon_addon.php' ) 
		&& ! is_plugin_active( 'wpcs-triggers-addon-premium/wpcs_triggers_addon.php' ) 
		) 
		) {
        $image_url = plugins_url( 'img/dashboardbannervertical.png', dirname( __FILE__ ) );
        ?>
        <div class="get-pro-banner">
			<a href="https://wpcontactslider.com/pricing/" target="_blank">
            	<img src="<?php echo esc_url( $image_url ); ?>" alt="Pro-Banner" style="width: 100%;">
			</a>
        </div>
        <?php
    }
}