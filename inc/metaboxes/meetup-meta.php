<div class="my_meta_control">
	
	<p class="howto"><?php printf( __( 'Enter your meetup data and have it displayed in your post using the %1$s shortcode.', 'meetup' ), '<code>[meetup]</code>'); ?></p>
 
	<p>
		<?php $mb->the_field('date'); ?>
		<label for="<?php $mb->the_name(); ?>"><?php _e( 'Meetup date', 'meetup' ); ?></label>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
		<span class="howto"><?php _e( 'Enter a date', 'meetup' ); ?></span>
	</p>
 
	<p>
		<?php $mb->the_field('time'); ?>
		<label for="<?php $mb->the_name(); ?>"><?php _e( 'Meetup time', 'meetup' ); ?></label>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
		<span class="howto"><?php _e( 'Enter a time, like 18:00 or 3.45pm', 'meetup' ); ?></span>
	</p>
 
	<p>
		<?php $mb->the_field('location'); ?>
		<label for="<?php $mb->the_name(); ?>"><?php _e( 'Meetup location', 'meetup' ); ?></label>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
		<span class="howto"><?php _e( 'Name of your location', 'meetup' ); ?></span>
	</p>
 
	<p>
		<?php $mb->the_field('location_url'); ?>
		<label for="<?php $mb->the_name(); ?>"><?php _e( 'Meetup location URL', 'meetup' ); ?></label>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
		<span class="howto"><?php _e( 'URL for your location, i.e. a Google Map', 'meetup' ); ?></span>
	</p>
 
	<p>
		<?php $mb->the_field('description'); ?>
		<label for="<?php $mb->the_name(); ?>"><?php _e( 'Meetup description', 'meetup' ); ?></label>
		<textarea name="<?php $mb->the_name(); ?>" rows="3"><?php $mb->the_value(); ?></textarea>
		<span class="howto"><?php _e( 'Optional notes, i.e. public transportation', 'meetup' ); ?></span>
	</p>

</div>