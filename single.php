<?php  get_header();

	global $post;

	echo '<section class="first-top-container">';

	($post->post_type == 'monument') ? get_template_part('/single/single', 'monument') :  get_template_part('/single/single', 'document');

	echo '</section>';

	get_footer();