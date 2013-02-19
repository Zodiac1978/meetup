<?php
/**
 * meetup Theme Options
 *
 * @package meetup
 * @since meetup 0.1
 */

/**
 * Register the form setting for our meetup_options array.
 *
 * This function is attached to the admin_init action hook.
 *
 * This call to register_setting() registers a validation callback, meetup_theme_options_validate(),
 * which is used when the option is saved, to ensure that our option values are properly
 * formatted, and safe.
 *
 * @since meetup 0.1
 */
function meetup_theme_options_init() {
	register_setting(
		'meetup_options', // Options group, see settings_fields() call in meetup_theme_options_render_page()
		'meetup_theme_options', // Database option, see meetup_get_theme_options()
		'meetup_theme_options_validate' // The sanitization callback, see meetup_theme_options_validate()
	);

	// Register our settings field group
	add_settings_section(
		'general', // Unique identifier for the settings section
		'', // Section title (we don't want one)
		'__return_false', // Section callback (we don't want anything)
		'theme_options' // Menu slug, used to uniquely identify the page; see meetup_theme_options_add_page()
	);

	// Register our individual settings fields
	add_settings_field( 'publisher', __( 'Google+ ID', 'meetup' ), 'meetup_settings_field_publisher', 'theme_options', 'general' );
	add_settings_field( 'ogimage', __( 'Default Open Graph image', 'meetup' ), 'meetup_settings_field_ogimage', 'theme_options', 'general' );
	add_settings_field( 'webfonts', __( 'Google Webfonts', 'meetup' ), 'meetup_settings_field_webfonts', 'theme_options', 'general' );
	add_settings_field( 'styles', __( 'Custom CSS Styles', 'meetup' ), 'meetup_settings_field_styles', 'theme_options', 'general' );
	add_settings_field( 'scripts', __( 'Custom Javascript', 'meetup' ), 'meetup_settings_field_scripts', 'theme_options', 'general' );
}
add_action( 'admin_init', 'meetup_theme_options_init' );

/**
 * Change the capability required to save the 'meetup_options' options group.
 *
 * @see meetup_theme_options_init() First parameter to register_setting() is the name of the options group.
 * @see meetup_theme_options_add_page() The edit_theme_options capability is used for viewing the page.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function meetup_option_page_capability( $capability ) {
	return 'edit_theme_options';
}
add_filter( 'option_page_capability_meetup_options', 'meetup_option_page_capability' );

/**
 * Add our theme options page to the admin menu.
 *
 * This function is attached to the admin_menu action hook.
 *
 * @since meetup 0.1
 */
function meetup_theme_options_add_page() {
	$theme_page = add_theme_page(
		__( 'Theme Options', 'meetup' ),   // Name of page
		__( 'Theme Options', 'meetup' ),   // Label in menu
		'edit_theme_options',          // Capability required
		'theme_options',               // Menu slug, used to uniquely identify the page
		'meetup_theme_options_render_page' // Function that renders the options page
	);
	
	add_action( "load-$theme_page", 'meetup_theme_options_page_help' );
}
add_action( 'admin_menu', 'meetup_theme_options_add_page' );

function meetup_theme_options_page_help() {
	global $wp_settings_fields;
        
	$screen = get_current_screen();
	$theme_name = function_exists( 'wp_get_theme' ) ? wp_get_theme() : get_current_theme();
	$help_path = get_template_directory() . '/inc/theme-options/help';
	$options = $wp_settings_fields['theme_options']['general'];
	
	$help = array();	
	foreach( $options as $option => $args ) {
		$help = array_merge( $help, array( $option )  );
	}
	
	// This works for WordPress 3.3 and up
	if ( method_exists( $screen, 'add_help_tab' ) ) {
		// Overview
		ob_start(); 		
		$overview = ( file_exists( $help_path . "/overview-help-tab.php" ) ) ? $help_path . "/overview-help-tab.php" : false;
	    include_once( $overview );
	    $overview = ob_get_contents();
	    ob_end_clean();
	    
		if( $overview ) {
			$screen->add_help_tab( array( 
							'title'		=> __( 'Overview', 'meetup' ), 
							'id' 		=> 'overview-help-tab', 
							'content' 	=> $overview
							) );
		}

		// Other help tabs
		foreach( $help as $key => $help_tab ) {
			ob_start();
			$help = ( file_exists( $help_path . "/$help_tab-help-tab.php" ) ) ? $help_path . "/$help_tab-help-tab.php" : false;	
		    include_once( $help );
		    $help = ob_get_contents();
		    ob_end_clean();
			
			if( $help ) {
				$screen->add_help_tab( array( 
								'title'		=> sprintf( __( "%s", 'meetup' ), $options[ $help_tab ]['title'] ), 
								'id' 		=> $options[ $help_tab ]['id'], 
								'content' 	=> $help
								) );
			}
		}
		
		// Help sidebar
		ob_start(); 		
		$sidebar = ( file_exists( $help_path . "/help-sidebar.php" ) ) ? $help_path . "/help-sidebar.php" : false;
	    include_once( $sidebar );
	    $sidebar = ob_get_contents();
	    ob_end_clean();

		if( $sidebar )
			$screen->set_help_sidebar( $sidebar );
	}
}

/**
 * Returns the options array for meetup.
 *
 * @since meetup 0.1
 */
function meetup_get_theme_options() {
	$saved = (array) get_option( 'meetup_theme_options' );
	$defaults = array(
		'publisher' => '',
		'ogimage'	=> '',
		'webfonts'	=> '',
		'styles'	=> '',
		'scripts'	=> '',
	);

	$defaults = apply_filters( 'meetup_default_theme_options', $defaults );

	$options = wp_parse_args( $saved, $defaults );
	$options = array_intersect_key( $options, $defaults );

	return $options;
}

/**
 * Renders the publisher text input setting field.
 */
function meetup_settings_field_publisher() {
	$options = meetup_get_theme_options();
	?>
	<input class="regular-text code" type="text" name="meetup_theme_options[publisher]" id="publisher" value="<?php echo esc_attr( $options['publisher'] ); ?>" />
	<br /><label class="description" for="publisher"><?php printf( __( 'Paste a %1$s ID here in order to tell Google who is the publisher of this site.', 'meetup' ), '<a href="https://plus.google.com/" target="_blank">Google +</a>' ); ?></label>
	<?php
}

/**
 * Renders the og:image URL text input setting field.
 */
function meetup_settings_field_ogimage() {
	$options = meetup_get_theme_options();
	?>
	<input class="regular-text code" type="text" name="meetup_theme_options[ogimage]" id="ogimage" value="<?php echo esc_url( $options['ogimage'] ); ?>" />
	<br /><label class="description" for="ogimage"><?php printf( __( 'Paste an image URL here to set a default preview image for social networks. Head over to %1$s to upload one.', 'meetup' ), '<a href="' . admin_url( 'upload.php' ) . '" target="_blank">' . __( 'Media', 'default' ) . '</a>' ); ?></label>
	<?php
}

/**
 * Renders the webfonts text input setting field.
 */
function meetup_settings_field_webfonts() {
	$options = meetup_get_theme_options();
	?>
	<input class="regular-text code" type="text" name="meetup_theme_options[webfonts]" id="webfonts" value="<?php echo esc_attr( $options['webfonts'] ); ?>" />
	<br /><label class="description" for="webfonts"><?php printf( __( 'Paste your %1$s here, or enter %2$s to disable webfonts completely.%3$s Default: %4$s', 'meetup' ), '<a href="' . __( 'http://www.google.com/webfonts', 'meetup' ) . '" target="_blank">' . __( 'Google webfonts', 'meetup' ) . '</a>', '<code>off</code>', '<br />', '<code>"PT+Sans:400,700,400italic:latin","Strait::latin"</code>' ); ?></label>
	<?php
}

/**
 * Renders the styles textarea setting field.
 */
function meetup_settings_field_styles() {
	$options = meetup_get_theme_options();
	?>
	<textarea class="large-text code" type="text" name="meetup_theme_options[styles]" id="styles" cols="50" rows="10" /><?php echo esc_textarea( $options['styles'] ); ?></textarea>
	<label class="description" for="styles"><?php printf( __( 'This goes in the %1$s section of your site. Remember to wrap your CSS into %2$s.%3$s If you are about to enter more than a few lines of CSS here, you might want to consider setting up a %4$s instead.', 'meetup' ), '<code>&lt;head&gt;</code>', '<code>&lt;style&gt;&hellip;&lt;/style&gt;</code>', '<br />', '<a href="http://codex.wordpress.org/Child_Themes" target="_blank">' . __( 'Child Theme', 'meetup' ) . '</a>' ); ?></label>
	<?php
}

/**
 * Renders the scripts textarea setting field.
 */
function meetup_settings_field_scripts() {
	$options = meetup_get_theme_options();
	?>
	<textarea class="large-text code" type="text" name="meetup_theme_options[scripts]" id="scripts" cols="50" rows="10" /><?php echo esc_textarea( $options['scripts'] ); ?></textarea>
	<label class="description" for="scripts"><?php printf( __( 'This goes in the footer section, somewhere before the %1$s tag. Remember to wrap your javascript into %2$s.%3$s Use %4$s for any custom jQuery code.', 'meetup' ), '<code>&lt;/body&gt;</code>', '<code>&lt;script&gt;&hellip;&lt;/script&gt;</code>', ' <span style="white-space:nowrap">[ ! ]</span>', '<a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script#jQuery_noConflict_wrappers" target="_blank">' . __( 'jQuery noConflict wrappers', 'meetup' ) . '</a>' ); ?></label>
	<?php
}

/**
 * Renders the Theme Options administration screen.
 *
 * @since meetup 0.1
 */
function meetup_theme_options_render_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<?php $theme_name = function_exists( 'wp_get_theme' ) ? wp_get_theme() : get_current_theme(); ?>
		<h2><?php printf( __( '%s Theme Options', 'meetup' ), $theme_name ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'meetup_options' );
				do_settings_sections( 'theme_options' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array where appropriate.
 *
 * @see meetup_theme_options_init()
 * @todo set up Reset Options action
 *
 * @param array $input Unknown values.
 * @return array Sanitized theme options ready to be stored in the database.
 *
 * @since meetup 0.1
 */
function meetup_theme_options_validate( $input ) {
	$output = array();
	
	// Social preview image URL
	if ( isset( $input['ogimage'] ) && ! empty( $input['ogimage'] ) )
		$output['ogimage'] = esc_url( $input['ogimage'] );
	
	// Google Plus ID 
	if ( isset( $input['publisher'] ) && ! empty( $input['publisher'] ) )
		$output['publisher'] = esc_attr( $input['publisher'] );
		
	// The webfonts text input must be safe text with no HTML tags
	if ( isset( $input['webfonts'] ) && ! empty( $input['webfonts'] ) )
		$output['webfonts'] = sanitize_text_field( $input['webfonts'] );

	// The styles textarea must be safe text with the allowed tags for posts
	if ( isset( $input['styles'] ) && ! empty( $input['styles'] ) )
		$output['styles'] = $input['styles'];
	
	// The scripts textarea must be safe text with the allowed tags for posts
	if ( isset( $input['scripts'] ) && ! empty( $input['scripts'] ) )
		$output['scripts'] = $input['scripts'];

	return apply_filters( 'meetup_theme_options_validate', $output, $input );
}