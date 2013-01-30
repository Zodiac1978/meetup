<?php
/**
 * @package meetup
 * @since meetup 0.1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if( ! has_post_format( 'image' ) 
				&& ! has_post_format( 'link' ) 
				&& ! has_post_format( 'quote' )
				&& ! has_post_format( 'video' ) ) : ?>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>

		<div class="entry-meta">
			<?php meetup_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">	
	<?php if( has_post_thumbnail() && false === get_post_format() ) : ?>
		<div class="entry-featured-image">
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'meetup' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
				<?php the_post_thumbnail( 'single' ); ?>
			</a>
			<?php if( function_exists( 'glueck_get_photo_credit_line' ) ) 
					echo glueck_get_photo_credit_line( get_post_thumbnail_id() ); ?>
		</div>
	<?php endif; ?>
		<?php the_content(); ?>
		<?php wp_link_pages(); ?>
	</div><!-- .entry-content -->
	
	<?php if( has_post_format( 'image' ) 
			|| has_post_format( 'link' )
			|| has_post_format( 'quote' )
			|| has_post_format( 'video' ) ) : ?>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>

		<div class="entry-meta">
			<?php meetup_posted_on(); ?>

	<?php else : ?>
	<footer class="entry-meta">
	<?php endif; ?>
	
		<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'meetup' ) );
				if ( $categories_list && meetup_categorized_blog() ) :
			?>
			<span class="cat-links">
				<i class="icon-archive"></i>&nbsp;<?php 
					printf( '<span class="assistive-text">%1$s</span> %2$s', 
							__( 'Posted in', 'meetup' ), 
							$categories_list 
							); ?>
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
		<i class="icon-bookmark"></i><?php 
			printf( '<a href="%1$s" title="%2$s %3$s" rel="bookmark">Permalink</a>', 
					get_permalink(),
					__( 'Permalink to', 'meetup' ),
					the_title_attribute( 'echo=0' )
					); ?>
		<?php endif; ?>
		
		<span class="sep"> | </span>
		<?php meetup_social_sharing( get_the_ID() ); ?>
		
		<?php edit_post_link( __( 'Edit', 'meetup' ), '<span class="sep"> | </span><span class="edit-link">', '</span>' ); ?>
		
		<?php if( false == get_post_format() ) : // Show author bio only for standard post format posts ?>	
			<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
			
			<?php meetup_the_author_box( get_the_author_meta( 'ID' ) ); ?>

			<?php endif; ?>
		<?php endif; ?>

	<?php if( has_post_format( 'image' ) 
			|| has_post_format( 'link' )
			|| has_post_format( 'quote' )
			|| has_post_format( 'video' ) ) : ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
	<?php else : ?>
	</footer><!-- .entry-meta -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
