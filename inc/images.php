<?php 

/**
 * Custom functions for image management
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package meetup
 * @since meetup 0.1
 */

/**
 * Output image attachments as <figure>
 * 
 * @since meetup 0.1
 */
function meetup_image_as_figure( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	
	$src  = wp_get_attachment_image_src( $id, $size, false );
	
	$align = ( $align ) ? ' align' . $align : ' alignnone';
	
	// Only images without captions
	$html  = ( $caption ) ? '' : '<figure id="post-' . $id . ' media-' . $id .'" class="wp-caption' . $align . '">';
	$html .= ( $url ) ? '<a href="' . esc_attr( $url ) . '" title="' . $title . '">' : '';
	$html .= '<img src="' . $src[0] . '" alt="' . $alt . '" width="' . $src[1] . '" height="' . $src[2] . '" />';
	$html .= ( $url ) ? '</a>' : '';

	/* Check for photographer credit provided by the Glück plugin */
	$credit_name = get_post_meta( $id, '_glueck_photographer_name', true );
	$credit_url = get_post_meta( $id, '_glueck_photographer_url', true );
	$credits = '';
	
	if( $credit_name ) {
		$credits .= ' <span class="credit-line">' . __( 'Photo: ', 'meetup' );
		$credits .= $credit_url ? '<a href="' . esc_url( $credit_url ) . '" title="' . esc_attr( $credit_name ) . '">' . esc_html( $credit_name ) . '</a>' : esc_html( $credit_name );
		$credits .= '</span>';
	}
	$html .= ( $caption ) ? $credits : $credits . '</figure>';
	
	return $html;
}
add_filter( 'image_send_to_editor', 'meetup_image_as_figure', 10, 9 );

/**
 * Custom image caption shortcode output
 * 
 * @since meetup 0.1
 */
function meetup_caption_shortcode( $output, $attr, $content ) {

	/* We're not worried abut captions in feeds, so just return the output here. */
	if ( is_feed() )
		return $output;

	/* Set up the default arguments. */
	$defaults = array(
		'id' => '',
		'align' => 'alignnone',
		'width' => '',
		'caption' => ''
	);

	/* Merge the defaults with user input. */
	$attr = shortcode_atts( $defaults, $attr );

	/* If the width is less than 1 or there is no caption, return the content wrapped between the [caption]< tags. */
	if ( 1 > $attr['width'] || empty( $attr['caption'] ) )
		return $content;
	
	/* Add extra pixels for image padding, border and box-shadow */
	$width = (int)( esc_attr( $attr['width'] ) + 16 );

	/* Set up the attributes for the caption <figure>. */
	$attributes = ( !empty( $attr['id'] ) ? ' id="' . esc_attr( $attr['id'] ) . '"' : '' );
	$attributes .= ' class="wp-caption ' . esc_attr( $attr['align'] ) . '"';
	$attributes .= ' style="width: ' . $width . 'px"';
	
	/* Output */
	$output = '<figure' . $attributes .'>' . "\n";
	$output .= do_shortcode( $content );
	$output .= '<figcaption class="wp-caption-text">' . $attr['caption'] . '</figcaption>' . "\n";
	$output .= '</figure>' . "\n";

	/* Return the formatted, clean caption. */
	return $output;
}
add_filter( 'img_caption_shortcode', 'meetup_caption_shortcode', 10, 3 );

/**
 * Custom gallery output
 * 
 * @since meetup 0.1
 */
function meetup_html5_gallery( $output, $attr ) {
	global $post;

	static $instance = 0;
	$instance++;
	
	// Remove this since we don't want an endless loop going on here
	/* 
	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr); 
	*/

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'  	=> 'ASC',
		'orderby'	=> 'menu_order ID',
		'id' 		=> $post->ID,
		'itemtag'	=> 'li',
		'icontag'	=> 'figure',
		'captiontag' => 'figcaption',
		'columns'	=> 3,
		'size'   	=> 'thumbnail',
		'include'	=> '',
		'exclude'	=> ''
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	
	// Ignore inline styles, left here for reference
	/*
	if ( apply_filters( 'use_default_gallery_style', true ) )
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
		</style>";
	*/
	
	$size_class = sanitize_html_class( $size );
	$gallery_div = "<section id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>\n<ul>";
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

		$output .= "<{$itemtag} class='gallery-item'>\n";
		$output .= "<{$icontag} class='gallery-icon'>\n";
		$output .= $link . "\n";
		$output .= "</{$icontag}>\n";
		
		if ( $captiontag && trim( $attachment->post_excerpt ) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		
		$output .= "</{$itemtag}>";
		
		// Ignore columns
		/*
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '<br style="clear: both" />';
		*/
	}

	$output .= "</ul>\n</section>\n";
	return $output;
}
add_filter( 'post_gallery', 'meetup_html5_gallery', 10, 2 );