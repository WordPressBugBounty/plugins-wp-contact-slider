<?php

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
		'title'    => __( 'Slider Settings', 'wpcs' ),
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
				'name' => __( 'Label text color', 'wpcs' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}lable_text_color",
				// Field description (optional)
				'desc' => __( 'Choose color for your text of label', 'wpcs' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#ffffff', 'wpcs' ),

			),
			// Label background color
			array(
				// Field name - Will be used as label
				'name' => __( 'Label background color', 'wpcs' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}lable_bg_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the background of label', 'wpcs' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#0c0000', 'wpcs' ),

			),
			// Label border color
			array(
				// Field name - Will be used as label
				'name' => __( 'Label border color', 'wpcs' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}lable_border_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the border of Slider', 'wpcs' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#7f7f7f', 'wpcs' ),
			),
			// slider text color
			array(
				// Field name - Will be used as label
				'name' => __( 'Slider text color', 'wpcs' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}slider_text_color",
				// Field description (optional)
				'desc' => __( 'Choose color for your text of Slider', 'wpcs' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#0c0000', 'wpcs' ),

			),
			// slider background color
			array(
				// Field name - Will be used as label
				'name' => __( 'Slider background color', 'wpcs' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}slider_bg_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the background of Slider', 'wpcs' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#ffffff', 'wpcs' ),

			),
			// Form background color
			array(
				// Field name - Will be used as label
				'name' => __( 'Form background color', 'wpcs' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}form_bg_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the for the background of form container', 'wpcs' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#fcfcfc', 'wpcs' ),
			),
			// Form border color
			array(
				// Field name - Will be used as label
				'name' => __( 'Form border color', 'wpcs' ),
				// Field ID, i.e. the meta key
				'id'   => "{$prefix}slider_border_color",
				// Field description (optional)
				'desc' => __( 'Choose color for the border of the form container', 'wpcs' ),
				'type' => 'color',
				// Default value (optional)
				'std'  => __( '#0c0000', 'wpcs' ),
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
				'name'    => __( 'Slider position', 'wpcs' ),
				'id'      => "{$prefix}slider_position", // Not used but needed for plugin
				'desc'    => __( 'Select the position of contact slider', 'wpcs' ),
				'options' => array(
					'left'  => __( ' Left ', 'wpcs' ),
					'right' => __( ' Right ', 'wpcs' ),
				),

			),

			array(
				'name'    => __( 'Enable scroll bar', 'wpcs' ),
				'id'      => "{$prefix}enable_form_scroll",
				'class'   => "{$prefix}enable_form_scroll",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Enable scroll bar in slider window<br><strong>Note</strong>: This will be helpful if you would like to avoid mouse scroll only', 'wpcs' ),
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'yes' => '',
				),
			),

			array(
				'name'    => __( 'Hide on small devices', 'wpcs' ),
				'id'      => "{$prefix}hide_on_mobile",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Check this box if you would like to hide this slider on small devices less then 500px', 'wpcs' ),
				'options' => array(
					'yes' => '',
				),
			),

			array(
				'name'    => __( 'Open slider by default', 'wpcs' ),
				'id'      => "{$prefix}open_form",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Check this box if you would like to open this slider by default when page is loaded', 'wpcs' ),
				'options' => array(
					'yes' => '',
				),
			),

			array(
				'name'    => __( 'Display on Home', 'wpcs' ),
				'id'      => "{$prefix}display_on_home",
				'class'   => "{$prefix}display_on_home",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Check this box if you would like display this contact slider on Home Page', 'wpcs' ),
				'options' => array(
					'yes' => '',
				),
			),
			array(
				'name'    => __( 'Display on All Pages', 'wpcs' ),
				'id'      => "{$prefix}display_on_all",
				'class'   => "{$prefix}display_on_all",
				'type'    => 'checkbox_list',
				'desc'    => __( 'Check this box if you would like to display this contact slider on All Pages<br><strong>Note</strong>: If you are going to check this option no other slider is going to work on any page.', 'wpcs' ),
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'yes' => '',
				),
			),

			array(
				'name'       => __( 'Display on Selected Posts & Pages', 'wpcs' ),
				'id'         => "{$prefix}pages",
				'type'       => 'post',
				'clone'      => true,
				// Post type
				'post_type'  => array( 'post', 'page' ),
				'std'        => __( 'All', 'wpcs' ),
				// Field type, either 'select' or 'select_advanced' (default)
				'field_type' => 'select_advanced',
				'desc'       => __( 'Select posts and pages where you want to display this slider', 'wpcs' ),
				// Query arguments (optional). No settings means get all published posts
				'query_args' => array(
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
				),
			),

			array(
				'name'    => __( 'What would you like to display?', 'wpcs' ),
				'id'      => "{$prefix}option",
				'type'    => 'radio',
				// Options of checkboxes, in format 'value' => 'Label'
				'options' => array(
					'shortcode' => __( 'Shortcode', 'wpcs' ),
					'html'      => __( 'Text or html', 'wpcs' ),
				),
			),

		),
	);

	// 2nd meta box for shortcode info
	$meta_boxes[] = array(
		'title'    => __( 'Shortcode', 'wpcs' ),
		'pages'    => array( 'wpcs' ),
		// Where the meta box appear: normal (default), advanced, side. Optional.
		'context'  => 'normal',
		// Order of meta box: high (default), low. Optional.
		'priority' => 'low',
		'fields'   => array(
			// shortcode
			array(
				'type'        => 'text',
				'name'        => __( 'Shortcode', 'wpcs' ),
				'id'          => "{$prefix}shortcode", // Not used but needed for plugin
				'desc'        => __( 'Enter your shortcode only if you want to use it in slider', 'wpcs' ),
				'placeholder' => __( 'Enter your short code', 'wpcs' ),

			),
			array(
				'type'    => 'select',
				'name'    => __( 'Type', 'wpcs' ),
				'id'      => "{$prefix}plugin_name", // Not used but needed for plugin
				'desc'    => __( 'Select the name of Plugin for which you want to use short code', 'wpcs' ),
				'options' => array(
					'none'                => __( 'none', 'wpcs' ),
					'gf'                  => __( 'Gravity Forms', 'wpcs' ),
					'cf7'                 => __( 'Contactform 7 ', 'wpcs' ),
					'wp-form'             => __( 'Wp-forms ', 'wpcs' ),
					'caldera-form'        => __( 'Caldera forms ', 'wpcs' ),
					'constant-forms'      => __( 'Constant Contact Forms ', 'wpcs' ),
					'pirate-forms'        => __( 'Pirate Forms ', 'wpcs' ),
					'si-contact-form'     => __( 'Fast Secure Contact Form', 'wpcs' ),
					'formidable'          => __( 'Formidable Forms', 'wpcs' ),
					'form-maker'          => __( 'Form Maker', 'wpcs' ),
					'form-craft'          => __( 'Form Craft', 'wpcs' ),
					'ninja-forms'         => __( 'Ninja Forms', 'wpcs' ),
					'visual-form-builder' => __( 'Visul Form Builder', 'wpcs' ),
					'mycred'              => __( 'Mycred', 'wpcs' ),
				),
			),
		),
	);

	// 3rd meta box for displaying text
	$meta_boxes[] = array(
		'title'    => __( 'Text', 'wpcs' ),
		'pages'    => array( 'wpcs' ),
		// Where the meta box appear: normal (default), advanced, side. Optional.
		'context'  => 'normal',
		// Order of meta box: high (default), low. Optional.
		'priority' => 'low',
		'fields'   => array(
			// shortcode
			array(
				'name'    => __( 'Text to display', 'wpcs' ),
				'id'      => "{$prefix}html", // Not used but needed for plugin
				'desc'    => __( 'Enter text or Html if you want to display', 'wpcs' ),
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
		<style>
			#post-body.columns-2 #side-sortables {
				min-height: 1px;
			}
			div#postbox-container-1{
				display: flex;
				flex-direction: column-reverse;
			}
		</style>
        <div class="get-pro-banner">
			<a href="https://wpcontactslider.com/pricing/?utm_source=wordpress&utm_medium=Plugin&utm_campaign=side_banner&utm_content=banner_image" target="_blank">
            	<img src="<?php echo esc_url( $image_url ); ?>" alt="Pro-Banner" style="width: 100%;">
			</a>
        </div>
        <?php
    }
}