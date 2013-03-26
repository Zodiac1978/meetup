<?php

$meetup_mb = new WPAlchemy_MetaBox(array (
	'id' => '_meetup_meta',
	'types' => array( 'post', 'page' ),
	'title' => __( 'Meetup Event Data', 'meetup' ),
	'template' => get_template_directory() . '/inc/metaboxes/meetup-meta.php',
));

/* eof */