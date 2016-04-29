<?php
/*
Template Name: My Folders
*/

global $current_user;

?>
<?php if (is_user_logged_in()) : ?>
<?php get_header(); ?>

<section id="my-folders-wrapper" class="first-top-container">
  <div class="row">
  	<div class="large-12 columns">
	  	<h1 id="my-fol-header">
	  		<span class="ico mycats-ico"> </span>
	  		<span> <?php _e('Moje Katalogi', 'otwarte2013');?> </span>
	  	</h1>
  	</div>
    <div id="my-cats-cnt" class="large-12 columns left">
      		<?php echo_user_folders(); ?>
  </div>
</div>
</section>

<section id="search-results" class="user-folder closed">
            <div id="ajax-wrapper" class="">

            </div>
</section>

<?php get_footer(); ?>

<?php else : ?>
<?php wp_redirect('/?login=my_folders') ?>

<?php endif ?>