<?php
/**
 * @package meetup
 * @since meetup 0.1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">
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
		
		<?php the_content( sprintf( __( 'Continue reading %1$s', 'meetup' ), '<i class="icon-right-dir"></i><span class="meta-nav assistive-text">&rarr;</span>' ) ); ?>
		<div class="page-links-preview">
			<?php wp_link_pages(); ?>
		</div>
	</div><!-- .entry-content -->

	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'meetup' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta">
			<?php meetup_posted_on(); ?>
			
			<span class="sep"> | </span>
			<?php // Hide category and tag text for pages on Search
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
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
</article><!-- #post-<?php the_ID(); ?> -->
