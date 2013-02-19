<?php
/**
 * Meetup functions and definitions
 *
 * @package meetup
 * @since meetup 0.1
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since meetup 0.1
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

if ( ! function_exists( 'meetup_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since meetup 0.1
 */
function meetup_setup() {

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );

	/**
	 * Custom functions that act independently of the theme templates
	 */
	require( get_template_directory() . '/inc/tweaks.php' );

	/**
	 * Custom Theme Options
	 */
	require( get_template_directory() . '/inc/theme-options/theme-options.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on meetup, use a find and replace
	 * to change 'meetup' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'meetup', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'single', '630', '240', true );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'meetup' ),
	) );

	/**
	 * Add support for the Aside Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'gallery', 'link', 'quote' ) );
	
	/**
	 * Add support for custom background
	 */
	add_theme_support( 'custom-background' );
	
	/**
	 * Add editor stylesheet
	 */
	add_editor_style();
}
endif; // meetup_setup
add_action( 'after_setup_theme', 'meetup_setup' );

/**
 * Register widgetized area and update sidebar with default widgets
 *
 * @since meetup 0.1
 */
function meetup_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'meetup' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'meetup' ),
		'id' => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Widget Area', 'meetup' ),
		'id' => 'colophon-widgets',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}
add_action( 'widgets_init', 'meetup_widgets_init' );

/**
 * Add Option: Custom CSS Styles
 *
 * @since meetup 0.4
 */
add_action( 'wp_head', 'meetup_custom_css', 12 );
function meetup_custom_css() { 

	$options = get_option( 'meetup_theme_options' );
	if( is_array( $options ) && isset( $options[ 'styles' ] ) )
		echo $options[ 'styles' ];
}

/**
 * Add Option: Custom Javascript
 *
 * @since meetup 0.4
 */
add_action( 'wp_footer', 'meetup_custom_js', 20 );
function meetup_custom_js() { 

	$options = get_option( 'meetup_theme_options' );
	if( is_array( $options ) && isset( $options[ 'scripts' ] ) )
		echo $options[ 'scripts' ];
}

/**
 * Add Option: Custom Webfonts
 *
 * @since meetup 0.4
 */
add_action( 'wp_head', 'meetup_google_webfonts', 12 );
function meetup_google_webfonts() { 
	
	$webfonts = '"PT+Sans:400,700,400italic:latin","Strait::latin"';
	
	$options = get_option( 'meetup_theme_options' );
	if( 'off' == $options[ 'webfonts' ] )
		return;
	if( is_array( $options ) && isset( $options[ 'webfonts' ] ) )
		$webfonts = $options[ 'webfonts' ];
?>
<script>//<!-- 
	WebFontConfig = { google: { families: [ <?php echo $webfonts; ?> ] } };
	(function() {
		var wf = document.createElement('script');
		wf.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
		wf.type = 'text/javascript';
		wf.async = 'true';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(wf, s);
	})(); // -->
</script>

<?php }

/**
 * Some custom javascript in the footer
 *
 * @since meetup 0.4
 */
add_action( 'wp_footer', 'meetup_custom_js_footer', 20 );
function meetup_custom_js_footer() { 
	global $wp_query;
		
	?>
	<script>//<!-- 
	(function($){
		$(document).ready(function(){
			$('.toggle').hide();
			$('a[href^="#"].toggler, .share > a').click(function(e){
				e.preventDefault();
				$($(this).attr('href')).slideToggle(250);
			});
			$('a[href^="#"]').not('.toggler, .share > a').click(function(e){
				e.preventDefault();
				scrolltarget = $('#' + this.href.split('#')[1] ).offset().top;
				$('html, body').animate({scrollTop:scrolltarget}, 250);
			});
	<?php  
	
		$posts = $wp_query->posts;
		
		foreach( $posts as $post ) : 
			$postID = $post->ID;
			/* Check if author has Twitter profile field */
			$getpost = get_post( $postID ); 
			$twitter = get_the_author_meta( 'twitter', $getpost->post_author  );
			$tweettext = esc_attr( $getpost->post_title );
			
			if( '' !== $twitter ) {
				/* The Twitter profile field holds an URL.
				   We only need the last chunk of it, because that's the username */
				$twitter = array_map( 'strrev', explode( '/', strrev( $twitter ) ) );
				$twitter = isset( $twitter ) && is_array( $twitter ) ? $twitter[0] : '';
				if( '' !== $twitter )
					$tweettext .= ' via @' . $twitter; 
			}
	?>
		$('#share-<?php echo $postID; ?>').socialSharePrivacy({uri : "<?php echo wp_get_shortlink( $postID ); ?>",services:{facebook:{'dummy_img':"<?php echo get_template_directory_uri(); ?>/images/socialshareprivacy/dummy_facebook.png"},twitter:{'dummy_img':"<?php echo get_template_directory_uri(); ?>/images/socialshareprivacy/dummy_twitter.png",'tweet_text':"<?php echo $tweettext; ?>"},gplus:{'dummy_img':"<?php echo get_template_directory_uri(); ?>/images/socialshareprivacy/dummy_gplus.png"}},'css_path':''});
	<?php endforeach; ?>
	});
	}(jQuery));
	// -->
	</script>

<?php }

/**
 * Prints jQuery in footer on front-end.
 */
function meetup_print_jquery_in_footer( &$scripts) {
	if ( ! is_admin() )
		$scripts->add_data( 'jquery', 'group', 1 );
}
add_action( 'wp_default_scripts', 'meetup_print_jquery_in_footer' );

/**
 * Enqueue scripts and styles
 */
function meetup_scripts() {
	
	// Version query. For development use 'date( 'YmdHis' )' and
	// outcommend the use of _meetup_remove_script_version() below
	$lastupdated = '20130112';
	
	// (Parent) Theme stylesheet. You can override this in a Child Theme
	// by passing an empty function to the filter like so:
	// add_filter( 'meetup_theme_stylesheet', '__return_zero' );
	wp_enqueue_style( 'theme', get_template_directory_uri() . '/css/theme.css', false, $lastupdated );
	
	// If you set up a Child Theme, this will call its style.css
	wp_enqueue_style( 'style', get_stylesheet_uri(), false, $lastupdated );
	
 	wp_enqueue_script( 'small-menu', get_template_directory_uri() . '/js/small-menu.min.js', array( 'jquery' ), '20121117', true );
 	
 	wp_enqueue_script( 'socialshareprivacy', get_template_directory_uri() . '/js/jquery.socialshareprivacy.min.js', array( 'jquery' ), '20121117', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
	 	wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.min.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'meetup_scripts' );

/**
 * Remove version queries from stylesheet and javascript calls
 */
function _meetup_remove_script_version( $src ){
	$parts = explode( '?', $src );
	return $parts[0];
}
add_filter( 'script_loader_src', '_meetup_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_meetup_remove_script_version', 15, 1 );

/**
 * Implement the Custom Header feature
 */
require( get_template_directory() . '/inc/custom-header.php' );

/**
 * Implement custom metaboxes
 */
require_once( get_template_directory() . '/inc/metaboxes/setup.php' );
require_once( get_template_directory() . '/inc/metaboxes/meetup-spec.php' );