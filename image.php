<?php
/**
 * The template for displaying image attachments.
 *
 * @package meetup
 * @since meetup 0.1
 */

get_header();
?>

		<div id="primary" class="content-area image-attachment full-width">
			<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
			<?php 
					/**
					 * Get all the attached images
					 */
					$attachments = array_values( get_children( 
														array( 
															'post_parent'	=> $post->post_parent, 
															'post_status'	=> 'inherit', 
															'post_type'		=> 'attachment', 
															'post_mime_type'=> 'image',
															'order'			=> 'ASC', 
															'orderby'		=> 'menu_order ID'
															) ) );
			?>
			<?php // If we have just 1 attachment, let's hide the image navigation
				  if ( count( $attachments ) > 1 ) : ?>
				<nav id="image-navigation" class="site-navigation">
					<span class="next-image"><?php next_image_link( false, sprintf( '%1$s<i class="icon-to-end"></i>', __( 'Next', 'meetup' ) ) ); ?></span>
					<span class="previous-image"><?php previous_image_link( false, sprintf( '<i class="icon-to-start"></i>%1$s', __( 'Previous', 'meetup' ) ) ); ?></span>
					<span class="back-to-post"><a href="<?php echo get_permalink( $post->post_parent ); ?> class="icon-cancel"><?php _e( 'Go back to post', 'meetup' ) ?></a></span>
				</nav><!-- #image-navigation -->
				<?php endif; ?>
				
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>

						<div class="entry-meta">
							<?php
								$metadata = wp_get_attachment_metadata();
								printf( __( 'Published <span class="entry-date"><time class="entry-date" datetime="%1$s" pubdate>%2$s</time></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%7$s</a>', 'meetup' ),
									esc_attr( get_the_date( 'c' ) ),
									esc_html( get_the_date() ),
									wp_get_attachment_url(),
									$metadata['width'],
									$metadata['height'],
									get_permalink( $post->post_parent ),
									get_the_title( $post->post_parent )
								);
							?>
							<?php edit_post_link( __( 'Edit', 'meetup' ), '<span class="sep"> | </span> <span class="edit-link">', '</span>' ); ?>
							<?php if ( count( $attachments ) == 1 ) : ?>
							<p class="back-to-post"><a href="<?php echo get_permalink( $post->post_parent ); ?>" class="icon-cancel"><?php _e( 'Go back to post', 'meetup' ) ?></a></p>
							<?php endif; ?>
						</div><!-- .entry-meta -->
					</header><!-- .entry-header -->

					<div class="entry-content">

						<div class="entry-attachment">
							<figure class="attachment">
								<?php
									/**
									 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
									 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
									 */
									foreach ( $attachments as $k => $attachment ) {
										if ( $attachment->ID == $post->ID )
											break;
									}
									$k++;
									// If there is more than 1 attachment in a gallery
									if ( count( $attachments ) > 1 ) {
										if ( isset( $attachments[ $k ] ) )
											// get the URL of the next image attachment
											$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
										else
											// or get the URL of the first image attachment
											$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
									} else {
										// or, if there's only 1 image, get the URL of the image
										$next_attachment_url = wp_get_attachment_url();
									}
								?>

								<a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
									$attachment_size = apply_filters( 'meetup_attachment_size', array( 1200, 1200 ) ); // Filterable image size.
									echo wp_get_attachment_image( $post->ID, $attachment_size );
								?></a>
								
								<?php if ( ! empty( $post->post_excerpt ) ) : ?>
								<figcaption class="entry-caption">
									<?php the_excerpt(); ?>
								</figcaption><!-- .entry-caption -->
								<?php endif; ?>
								
								<?php 
									/**
									 * Photographer credit line if the Glück plugin is active
									 */
										if( function_exists( 'glueck_get_photo_credit_line' ) ) :
											echo glueck_get_photo_credit_line( $attachment->ID );
									  	endif ?>
								
							</figure><!-- .attachment -->
						</div><!-- .entry-attachment -->

						<div class="entry-description">
							<?php the_content(); ?>
						</div>
						<?php wp_link_pages(); ?>

					</div><!-- .entry-content -->

					<footer class="entry-meta">
						<?php if ( comments_open() && pings_open() ) : // Comments and trackbacks open ?>
							<?php printf( __( '<a class="comment-link" href="#respond" title="Post a comment">Post a comment</a> or leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'meetup' ), get_trackback_url() ); ?>
						<?php elseif ( ! comments_open() && pings_open() ) : // Only trackbacks open ?>
							<?php printf( __( 'Comments are closed, but you can leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'meetup' ), get_trackback_url() ); ?>
						<?php elseif ( comments_open() && ! pings_open() ) : // Only comments open ?>
							<?php _e( 'Trackbacks are closed, but you can <a class="comment-link" href="#respond" title="Post a comment">post a comment</a>.', 'meetup' ); ?>
						<?php elseif ( ! comments_open() && ! pings_open() ) : // Comments and trackbacks closed ?>
							<?php _e( 'Both comments and trackbacks are currently closed.', 'meetup' ); ?>
						<?php endif; ?>
						<span class="sep"> | </span>
						
						<?php meetup_social_sharing( get_the_ID() ); ?>
						
						<?php edit_post_link( __( 'Edit', 'meetup' ), '<span class="sep"> | </span> <span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->
				</article><!-- #post-<?php the_ID(); ?> -->

				<?php comments_template(); ?>

			<?php endwhile; // end of the loop. ?>

			</div><!-- #content .site-content -->
		</div><!-- #primary .content-area .image-attachment -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>