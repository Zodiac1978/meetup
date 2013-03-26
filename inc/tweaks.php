<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package meetup
 * @since meetup 0.1
 */

/**
 * Comment form defaults
 *
  * @since meetup 0.4
 */
add_filter( 'comment_form_defaults', 'meetup_comment_form_defaults' );
function meetup_comment_form_defaults( $defaults ){

	$defaults['comment_notes_before'] = '<p class="comment-notes">' . __( 'Name and email are required. Your email address will not be published. URL is optional. Be nice!', 'meetup' ) . '</p>';
	$defaults['comment_notes_after'] = '<p class="form-allowed-tags"><a href="#allowed-tags" class="toggler">' . sprintf( __( 'Some <abbr title="HyperText Markup Language">HTML</abbr> allowed… %s', 'meetup' ) . '</a>', ' <code id="allowed-tags" class="toggle">' . allowed_tags() . '</code>' ) . '</p>';
	
	return $defaults;
}
/**
 * Fallback for empty post title
 * 
 * @since meetup 0.4
 */
add_filter( 'the_title', 'meetup_post_title', 20, 2 );
function meetup_post_title( $title, $id ) {
	$title = ( $title ) ? $title : '&nbsp;';
	return $title;
}

/**
 * Define global arguments for inside-post pagination
 *
 * @since meetup 0.1
 */
function meetup_link_pages_args() {
	$args = array( 
			'before' 	=> '<div class="page-links"><i class="page-links-label icon-book-open" title="' . __( 'Pages', 'meetup' ) . '"></i><span class="page-links-items">', 
			'after' 	=> '</span></div>',
			'link_before' => '', 
			'link_after' => '',
			'next_or_number' => 'number', 
			'nextpagelink' => __( 'Next page', 'meetup' ),
			'previouspagelink' => __( 'Previous page', 'meetup' ),
			'pagelink' 	=> '<span class="page-link-item">%</span>',
			'echo' => 1
			);
	
	return $args;
}
add_filter( 'wp_link_pages_args', 'meetup_link_pages_args' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @since meetup 0.1
 */
function meetup_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'meetup_page_menu_args' );

/**
 * Nav menu fallback
 * 
 * @since meetup 0.1
 */
function meetup_page_menu() {
	echo '<div class="alert alert-warning"><p class="alert-block icon-info-circled">' . sprintf( __( 'Please %1$screate a page menu%2$s for your site and set it as "Primary Menu".', 'meetup' ), '<a href="' . admin_url() . 'nav-menus.php">', '</a>' ) . '</p></div>';
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @since meetup 0.1
 */
function meetup_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'meetup_body_classes' );

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 *
 * @since meetup 0.1
 */
function meetup_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
add_filter( 'attachment_link', 'meetup_enhanced_image_navigation', 10, 2 );

/**
 * Shortcode for Meetup info box
 * 
 * @uses meetup_get_the_meetup()
 * @since meetup 0.1
 */
add_shortcode( 'meetup', 'meetup_event_shortcode' );
function meetup_event_shortcode( $atts, $content = null ) {
	global $post;
	
	extract( shortcode_atts( array(
		 'class'=> '',
		 'id'	=> '',
		 'title' => ''
    ), $atts ) );

	$class = ( $class ) ? $class : '';
	$id = ( $id ) ? $id : $post->ID;
	$title = ( $title ) ? $title : '';
	
	$output = ( $id && function_exists( 'meetup_get_the_meetup' ) ) ? meetup_get_the_meetup( $id, $class, $title ) : '';

    return $output;
}

/**
 * Textarea first in comment form
 *
 * @since meetup 0.2
 */
add_filter( 'comment_form_defaults', 'meetup_textarea_on_top', 100 );
add_action( 'comment_form_top', 'meetup_textarea_on_top' );
function meetup_textarea_on_top( $input = array () ) {
    static $textarea = '';

	if ( 'comment_form_defaults' === current_filter() ) {
		$textarea = $input['comment_field'];
		$input['comment_field'] = '';
		return $input;
	}

	print $textarea;
}