<?php
/**
 * @package meetup
 * @since meetup 0.1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'meetup' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

		<?php if( in_array( 'sticky', get_post_class() ) ) :
		/* Note: is_sticky() would return true here even if this is a stick post 
		   somewhere later in the loop where the applied post class does not 
		   contain 'sticky' anymore! */
		 ?>
		<div class="sticky-ribbon-wrapper">
			<div class="sticky-ribbon">
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'meetup' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php echo _x( 'Read this!', 'Sticky post label in the loop.', 'meetup' ); ?></a>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta">
			<?php meetup_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php if ( is_search() && false === get_post_format() ) : // Only display Excerpts for Search for standard posts ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
	<?php if( has_post_thumbnail() && false === get_post_format() ) : ?>
		<div class="entry-featured-image">
			<a href="<?php 
				the_permalink(); 
				?>" title="<?php 
				echo esc_attr( sprintf( __( 'Permalink to %s', 'meetup' ), the_title_attribute( 'echo=0' ) ) ); 
				?>" rel="bookmark"><?php 
				if ( is_sticky() ) :
					the_post_thumbnail( 'single' ); 
				else :
					the_post_thumbnail( 'thumbnail' ); 
				endif;
				
				?></a>
				<?php if( function_exists( 'glueck_get_photo_credit_line' ) ) 
						echo glueck_get_photo_credit_line( get_post_thumbnail_id() ); ?>
		</div>
	<?php endif; ?>
		<?php the_content( '' ); ?>
		<?php wp_link_pages(); ?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<footer class="entry-meta">
		<?php meetup_more_link(); ?>
		
		<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'meetup' ) );
				if ( $categories_list && meetup_categorized_blog() ) :
			?>
			<span class="cat-links">
				<i class="icon-archive"></i>&nbsp;<?php printf( '<span class="assistive-text">%1$s</span> %2$s', __( 'Posted in', 'meetup' ), $categories_list ); ?>
			</span>
			<?php endif; // End if categories ?>
			
			<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', __( ', ', 'meetup' ) );
				if ( $tags_list ) :
			?>
			<span class="sep"> | </span>
			<span class="tags-links">
				<i class="icon-tag"></i>&nbsp;<?php printf( '<span class="assistive-text">%1$s</span> %2$s', __( 'Tagged', 'meetup' ), $tags_list ); ?>
			</span>
			<?php endif; // End if $tags_list ?>
		<?php endif; // End if 'post' == get_post_type() ?>
		
		<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
		<span class="sep"> | </span>
		<span class="comments-link"><i class="icon-comment"></i>&nbsp;<?php comments_popup_link( __( 'Leave a comment', 'meetup' ), __( '1 Comment', 'meetup' ), __( '% Comments', 'meetup' ) ); ?></span>
		<?php endif; ?>
		
		<span class="sep"> | </span>
		<?php meetup_social_sharing( get_the_ID() ); ?>

		<?php edit_post_link( __( 'Edit', 'meetup' ), '<span class="sep"> | </span><span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
