<?php
/**
 * The Template for displaying all single posts.
 *
 * @package meetup
 * @since meetup 0.1
 */

get_header(); ?>

		<div id="primary" class="content-area<?php if( has_post_format( 'gallery' ) 
														|| has_post_format( 'image' )
														|| has_post_format( 'video' ) ) : ?> full-width<?php endif; ?>">
			<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php meetup_content_nav( 'nav-above' ); ?>
				
				<?php get_template_part( 'content', 'single'  ); ?>

				<?php meetup_content_nav( 'nav-below' ); ?>

				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template( '', true );
				?>

			<?php endwhile; // end of the loop. ?>

			</div><!-- #content .site-content -->
		</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>