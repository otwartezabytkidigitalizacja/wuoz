<?php get_header();
global $post;
echo '<section class="first-top-container">';
?>
<?php while ( have_posts() ) : the_post(); ?>
<div class="row">
	<div class="large-centered columns large-8 page-content">
		<h1>
		<?php if (has_post_thumbnail($post->ID) ) :  ?>
			<span class="ico"><?php the_post_thumbnail( ); ?></span>
		<?php endif ?>
		<?php echo $post->post_title ?>
		</h1>
		<p>
	<?php the_content() ?>
		</p>
	</div>
</div>
<?php endwhile; // end of the loop. ?>
<?php
echo "</section>";
get_footer(); ?>