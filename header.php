<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package meetup
 * @since meetup 0.1
 */

/* Get Theme Options. */
$options = get_option( 'meetup_theme_options' );

?><!DOCTYPE html>
<!--[if lt IE 9]><html class="lt-ie9 ie" <?php language_attributes(); ?> id="<?php echo apply_filters( 'meetup_html_id', 'wpm' ); ?>"><![endif]-->
<!--[if gte IE 9]><html class="gte-ie9 ie" <?php language_attributes(); ?> id="<?php echo apply_filters( 'meetup_html_id', 'wpm' ); ?>"><![endif]-->
<!--[if ! IE]><!--> <html class="no-ie" <?php language_attributes(); ?> id="<?php echo apply_filters( 'meetup_html_id', 'wpm' ); ?>"><!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php 
	/** 
	 * Chrome Frame as soon as possible after charset,
	 * beacuase IE starts reading again from the top when it
	 * hits this meta tag!
 	*/ ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
<title><?php
	/**
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged, $post;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'meetup' ), max( $paged, $page ) );

	?></title>
<meta name="description" content="<?php 
	if( function_exists( 'wpseo_the_desc' ) )
		do_action( 'wpseo_the_desc' );
	elseif( is_singular() )
		echo esc_html( meetup_excerpt( $post->ID ) ); 
	else 
		echo get_bloginfo( 'description', 'display' );
	?>" />
<?php 
	/**
	 * Set the Open Graph preview image for G+, FB etc.
	 * You can define a default image via Theme Options
	*/
	
	// If we have a featured image, pick it!	
	if ( is_singular() && has_post_thumbnail() ) :
		$og_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); 
		$og_image = $og_image[0];

	// Otherwise pick default image defined via options.
	elseif( is_array( $options ) && isset( $options[ 'ogimage' ] ) ) :
		$og_image = $options[ 'ogimage' ];
	
	// If everything goes wrong, screw it.
	else :
		$og_image = '';
	endif;
?>
<meta property="og:image" content="<?php echo $og_image; ?>" />
<meta property="og:site_name" content="<?php bloginfo( 'name' ); if ( $site_description ) echo " · $site_description"; ?>" />
<meta property="og:locale" content="<?php language_attributes(); ?>" />
<meta property="og:locale:alternate" content="<?php language_attributes(); ?>" />
<meta property="og:type" content="article" />
<meta property="og:title" content="<?php 
	if( function_exists( 'wpseo_the_title' ) )
		do_action( 'wpseo_the_title' ); 
	else
		the_title();
	?>" />
<meta property="og:url" content="<?php 
	if( is_singular() )
		the_permalink(); 
	else
		echo home_url();
	
	?>" />
<meta property="og:description" content="<?php 
	if( function_exists( 'wpseo_the_desc' ) )
		do_action( 'wpseo_the_desc' );
	elseif( is_singular() )
		echo esc_html( meetup_excerpt( $post->ID ) ); 
	else 
		echo get_bloginfo( 'description', 'display' );
	?>" />
<meta http-equiv="cleartype" content="on" />

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php 
	
	/* Set publisher link for G+ */
	if( is_array( $options ) && isset( $options[ 'publisher' ] ) )
		echo '<link rel="publisher" href="https://plus.google.com/' . $options[ 'publisher' ] . '" />';
 ?>
 
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>

<!--[if IE 7]>
<link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/css/entypo-ie7.css'; ?>" />
<![endif]-->

</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<?php 
			do_action( 'before' ); 
			$header_image = get_header_image();
			$header_image_text  = display_header_text();
			?>
	<header id="masthead" class="site-header<?php 
			
			if ( $header_image ) : ?> has-header-image<?php endif; 
			echo ( $header_image_text ) ? ' has-header-text' : ' no-header-text'; 
			
			?>" role="banner">
		<hgroup>
			<h1 class="site-title"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php echo apply_filters( 'meetup_site_title_text', get_bloginfo( 'name' ) ); ?></a></h1>
			<h2 class="site-description"><?php echo apply_filters( 'meetup_site_desc_text', get_bloginfo( 'description' ) ); ?></h2>
		</hgroup>
		
			<?php
				// Check to see if the header image has been removed
				if ( $header_image ) :
					// Compatibility with versions of WordPress prior to 3.4.
					if ( function_exists( 'get_custom_header' ) ) {
						// We need to figure out what the minimum width should be for our featured image.
						// This result would be the suggested width if the theme were to implement flexible widths.
						$header_image_width = get_theme_support( 'custom-header', 'width' );
					} else {
						$header_image_width = HEADER_IMAGE_WIDTH;
					}
					?>
			<a class="site-image" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php
					// The header image
					// Compatibility with versions of WordPress prior to 3.4.
					if ( function_exists( 'get_custom_header' ) ) {
						$header_image_width  = get_custom_header()->width;
						$header_image_height = get_custom_header()->height;
					} else {
						$header_image_width  = HEADER_IMAGE_WIDTH;
						$header_image_height = HEADER_IMAGE_HEIGHT;
					}
					?>
					<img src="<?php header_image(); ?>" width="<?php echo $header_image_width; ?>" height="<?php echo $header_image_height; ?>" alt="" />
			</a>
			<?php endif; // end check for removed header image ?>

		<nav role="navigation" class="site-navigation main-navigation clearfix">
			<h1 class="assistive-text"><?php _e( 'Menu', 'meetup' ); ?></h1>
			<div class="assistive-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'meetup' ); ?>"><?php _e( 'Skip to content', 'meetup' ); ?></a></div>

			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'fallback_cb' => 'meetup_page_menu' ) ); ?>
		</nav><!-- .site-navigation .main-navigation -->
	</header><!-- #masthead .site-header -->

	<div id="main" class="site-main">