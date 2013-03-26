<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package meetup
 * @since meetup 0.1
 */


/**
 * Credit line in footer.php, modify via custom filter
 * 
 * @since meetup 0.6
 */
function meetup_footer_credits(){ ?>

	<a href="#wpm" title="<?php esc_attr_e( 'Flyin’ high in the friendly sky…', 'meetup'); ?>" class="up"><i class="icon-up-circled"></i></a>
	<?php if ( get_page_by_path( 'impressum' ) ) : ?>
	<span class="sep"> | </span>
	<a href="<?php echo get_permalink( get_page_by_path( 'impressum' ) ) ?>" title="<?php echo esc_attr( get_the_title( get_page_by_path( 'impressum' ) ) ); ?>"><?php echo get_the_title( get_page_by_path( 'impressum' ) ); ?></a>
	<?php endif; ?>
	<span class="sep"> | </span>
	<a href="http://wordpress.org/" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'meetup' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'meetup' ), 'WordPress' ); ?></a>
	<span class="sep"> | </span>
	<?php printf( __( 'Theme: %1$s by %2$s.', 'meetup' ), 'Meetup', '<a href="http://glueckpress.com/" rel="designer">Gl&uuml;ckPress</a>' ); ?>

<?php }

/**
 * Hacked post excerpt function to be used outside the Loop.
 * From here: http://wordpress.stackexchange.com/a/26987/23011
 * 
 * @since meetup 0.5
 */
function meetup_excerpt( $post_or_id, $excerpt_more = ' […]' ){
	if ( is_object( $post_or_id ) ) 
		$postObj = $post_or_id;
	else 
		$postObj = get_post( $post_or_id );
	
	$raw_excerpt = $text = $postObj->post_excerpt;
	if ( '' == $text ) {
		$text = $postObj->post_content;
		
		$text = strip_shortcodes( $text );
		
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = apply_filters('excerpt_length', 100);
		
		// don't automatically assume we will be using the global "read more" link provided by the theme
		// $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

/**
 * Print social sharing buttons
 * Must be used within the Loop.
 * 
 * @param $postID (optional) Post ID to activate social sharing for
 * @since meetup 0.5
 */
if( ! function_exists( 'meetup_social_sharing' ) ) :
function meetup_social_sharing( $postID = null ) { 
	$postID = null !== $postID ? $postID : get_the_ID();
	?>
	<span class="share"><a href="#share-<?php echo $postID; ?>" title="<?php esc_attr_e( 'Share this entry', 'meetup' ); ?>" class="icon-export"><?php _e( 'Share', 'meetup' ); ?></a></span>
	<div id="share-<?php echo $postID; ?>" class="share-buttons toggle"></div>
<? }
endif;

/**
 * Print more-link if post content has <!--more--> tag
 * Must be used within the Loop.
 * 
 * @since meetup 0.4
 */
function meetup_more_link() {
	global $post;

	if( $post && isset( $post->post_content ) && $pos = strpos( $post->post_content, '<!--more-->' ) ) {
		printf( '<a href="%1$s#more-%2$s" class="more-link">%3$s</a>'
				,get_permalink( $post->ID )
				,$post->ID
				, __( 'Continue reading', 'meetup' ) . '&nbsp;<i class="icon-right-dir"></i><span class="meta-nav assistive-text">&rarr;</span>'
				);
	}
}

/**
 * Display an author box
 * 
 * @param $userID (required) User ID to retrieve info from
 * @uses meetup_author_contact_links()
 * @since meetup 0.4
 */
function meetup_the_author_box( $userID ) { ?>
	<div class="entry-author">
		<?php echo get_avatar( get_the_author_meta( 'user_email', $userID ), apply_filters( 'meetup_author_bio_avatar_size', 100 ) ); ?>
		<div class="author-description">
			<h4 class="posted-by"><?php printf( 
								 '<span class="author-by">%1$s </span><cite class="fn"><a href="%2$s" title="%3$s" rel="author">%4$s</a></cite>'
								,__( 'Posted by', 'meetup' )
								,get_author_posts_url( $userID )
								,esc_attr( get_the_author_meta( 'display_name', $userID ) )
								,get_the_author_meta( 'display_name', $userID )
							); ?></h4>
			<p><?php echo get_the_author_meta( 'description', $userID ); ?></p>
			<?php if( function_exists( 'meetup_author_contact_links' ) ) : ?>
			<div class="author-contact">
				<?php echo meetup_author_contact_links(); ?>
			</div>
			<?php endif; ?>
		</div><!-- end .author-description -->			
	</div><!-- end .entry-author -->
<?php }

/**
 * Get user contact URLs as links
 * 
 * @param $userID (required) User ID to retrieve info from
 * @param $wrapper (optional) HTML element to wrap all links in, default: ul
 * @param $link_wrapper (optional) HTML element to wrap each link in, default: li
 *
 * @since meetup 0.4
 */
function meetup_author_contact_links( $userID = null, $wrapper = 'ul', $link_wrapper = 'li' ) {
	
	$contactfields = array(
							 'twitter'	=> array( 'Twitter', get_the_author_meta( 'twitter', $userID ) )
							,'gplus'	=> array( 'Google+', get_the_author_meta( 'googleplus', $userID ) )
							,'facebook'	=> array( 'Facebook', get_the_author_meta( 'facebook', $userID ) )
							,'xing' 	=> array( 'XING', get_the_author_meta( 'xing', $userID ) )
							);
	if( empty( $contactfields ) )
		return;
	
	$wrapper = ( $wrapper ) 
				? array( 'open' => sprintf( '<%s class="author-contact-methods">', $wrapper ), 'close' => sprintf( '</%s>', $wrapper ) )
				: array( 'open' => '', 'close' => '' );
	$link_wrapper = ( $link_wrapper ) 
					? array( 
							 'open' => sprintf( '<%s>', $link_wrapper )
							,'close' => sprintf( '</%s>', $link_wrapper ) 
							)
					: array( 'open' => '', 'close' => '' );
	
	$output = $wrapper['open'] . "\n";
	
	foreach( $contactfields as $id => $field ) {
		$output .= ( ! $field[1] ) 
					? '' 
					: sprintf(
							 $link_wrapper['open'] . 
							 '<a href="%1$s" class="%2$s icon-%5$s" title="%3$s">%4$s</a>' .
							 $link_wrapper['close']
							,$field[1]
							,sprintf( _x( 'author-contact-link-%1$s', 'Class name for author %2$s link', 'meetup' ), $id, $field[0] )
							,sprintf( __( 'Follow me on %s', 'meetup' ), $field[0] )
							,sprintf( '<span>%s</span>', $field[0] )
							,$id
						) . "\n";
	}
	
	$output .= $wrapper['close'] . "\n";
	
	return $output;
}

if ( ! function_exists( 'meetup_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 *
 * @since meetup 0.1
 */
function meetup_content_nav( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = 'site-navigation paging-navigation';
	if ( is_single() )
		$nav_class = 'site-navigation post-navigation';

	?>
	<nav role="navigation" id="<?php echo $nav_id; ?>" class="<?php echo $nav_class; ?>">
		<h1 class="assistive-text"><?php _e( 'Post navigation', 'meetup' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . sprintf( _x( '%1$s', 'Previous post link', 'meetup' ), '<i class="icon-to-start"></i>' ) . '</span> %title' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . sprintf( _x( '%1$s', 'Next post link', 'meetup' ), '<i class="icon-to-end"></i>' ) . '</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( sprintf( '<span class="meta-nav"><i class="icon-fast-backward"></i></span>%1$s', __( 'Older posts', 'meetup' ) ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( sprintf( '%1$s<span class="meta-nav"><i class="icon-fast-forward"></i></span>', __( 'Newer posts', 'meetup' ) ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo $nav_id; ?> -->
	<?php
}
endif; // meetup_content_nav

if ( ! function_exists( 'meetup_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since meetup 0.1
 */
function meetup_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'meetup' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'meetup' ), ' ' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-header">
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 50 ); ?>
					<?php printf( __( '%s <span class="says">says:</span>', 'meetup' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
				</div><!-- .comment-author .vcard -->

				<div class="comment-meta commentmetadata">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'meetup' ), get_comment_date(), get_comment_time() ); ?>
					</time></a>
					<?php edit_comment_link( __( '(Edit)', 'meetup' ), ' ' );
					?>
				</div><!-- .comment-meta .commentmetadata -->
			</footer>

			<div class="comment-content">
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<div class="alert"><?php _e( 'Your comment is awaiting moderation.', 'meetup' ); ?></div>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for meetup_comment()

if ( ! function_exists( 'meetup_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since meetup 0.1
 */
function meetup_posted_on() {
	printf( '<i class="icon-calendar" title="%8$s"></i>' . __( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="byline"> by <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'meetup' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'meetup' ), get_the_author() ) ),
		esc_html( get_the_author() ),
		esc_attr__( 'Post date', 'meetup' )
	);
}
endif;

if( ! function_exists( 'meetup_get_the_meetup' ) ) :
/**
 * Retrieve custom meetup meta data
 * 
 * @since meetup 0.1
 */
function meetup_get_the_meetup( $post_id = '', $class = null, $title = null ) {
	global $meetup_mb, $post;
	if( ! $meetup_mb )
		return;
	
	$post_id = ( empty( $post_id ) ) ? $post->ID : $post_id;
	
	$event = $meetup_mb->the_meta( $post_id ); 
	if( empty( $event ) )
		return;

	$class = ( $class ) ? 'meetup-meetup-data ' . $class : 'meetup-meetup-data alert-info';
	$title = ( $title ) ? $title :  __( 'Meetup', 'meetup' );
	$location = ( $event['location'] && $event['location_url'] ) ? sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', $event['location_url'], $event['location'] ) : $event['location'];
	
	$output  = '';
	$output .= '<aside id="meetup-' . $post_id . '" class="' . $class . '">' . "\n";
	$output .= '<h4>' .$title . '</h4>' . "\n<ul>\n";
	$output .= ( $event['date'] ) ? '<li>' . sprintf( '<span>%1$s: </span> %2$s', __( 'Date', 'meetup' ), $event['date'] ) . '</li>' : '';
	$output .= ( $event['time'] ) ? '<li>' . sprintf( '<span>%1$s: </span> %2$s', __( 'Time', 'meetup' ), $event['time'] ) . '</li>' : '';
	$output .= ( $location ) ? '<li>' . sprintf( '<span>%1$s: </span> %2$s', __( 'Location', 'meetup' ), $location ) . '</li>' : '';
	$output .= ( $event['description'] ) ? '<li>' . sprintf( '<span>%1$s: </span> %2$s', __( 'Description', 'meetup' ), $event['description'] ) . '</li>' : '';
	$output .= "</ul>\n</aside><!-- .meetup-meetup-data -->";
	
	return $output;
}
endif;

/**
 * Print function meetup_get_the_meetup()
 * 
 * @since meetup 0.1
 */
function meetup_the_meetup() {
	if( function_exists( 'meetup_get_the_meetup' ) )
		echo meetup_get_the_meetup();
	else
		return;
}

/**
 * Returns true if a blog has more than 1 category
 *
 * @since meetup 0.1
 */
function meetup_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so meetup_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so meetup_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in meetup_categorized_blog
 *
 * @since meetup 0.1
 */
function meetup_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'meetup_category_transient_flusher' );
add_action( 'save_post', 'meetup_category_transient_flusher' );