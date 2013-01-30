<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package meetup
 * @since meetup 0.1
 */
?>

	</div><!-- #main .site-main -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<?php if( is_active_sidebar( 'colophon-widgets' ) ) : ?>
		<div class="widget-area" role="supplementary">
			<?php dynamic_sidebar( 'colophon-widgets' ); ?>
		</div><!-- .widget-area -->
		<?php endif; ?>
		
		<div class="site-info">
			<?php apply_filters( 'meetup_credits', meetup_footer_credits() ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon .site-footer -->
</div><!-- #page .hfeed .site -->

<?php wp_footer(); ?>

</body>
</html>