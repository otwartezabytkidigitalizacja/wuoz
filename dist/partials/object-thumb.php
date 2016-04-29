<?php // single object thumbnail
$logged = is_user_logged_in();
global $folders_page;
?>
<?php
echo "<pre>";
$terms = wp_get_post_terms($post->ID, $post->post_type .'_type');
//var_dump($terms);
echo "</pre>";?>
<div class="large-3 columns <?php echo 

$post->post_type;?> <?php echo 

isset($terms[0]) ? $terms[0]->slug : ''; ?> ">
  <div class="single-thumb " data-post-id="<?php echo 

$post->ID?>" data-post-type="<?php echo 

$post->post_type;?>" data-related-documents="<?php echo 

get_post_meta( $post->ID, 'oz_documents', true );?>">

    <?php if ($folders_page) : ?>
            <span class="ico rem-from-fol"> </span>
    <?php endif; ?>

    <a href=" <?php echo 

get_permalink();?>">
      <?php if ( has_post_thumbnail() ) : ?>
      <?php  $mobileThumb = wp_get_attachment_thumb_url (get_post_thumbnail_id( $post->ID ));
          $o_args = array(
          'data-interchange' => '[' . $mobileThumb .', ((min-width: 400px) and (max-width:768px)]'
            );
          $image = get_the_post_thumbnail(
          $post->ID,
          'default-thumb',
          $o_args
        );

            print_r($image);

          ?>
          <?php else : ?>
          <img src="<?php echo 

get_template_directory_uri() . '/img/' . $post->post_type; ?>_thumb270x200.png" alt="default_thumbnail"/>
        <?php endif ?>
        <h4 class="label">
          <span class="title-text"> <?php the_title();?> </span>
          <?php if ($logged) : ?>

          <?php if (is_fav($post->ID)) : ?>
            <span class="ico fav-star-ico alreadyfav"> </span>
          <?php else : ?>
            <span class="ico fav-star-ico"> </span>
          <?php endif?>

    <?php endif ?>
            <span class="gradient-text"></span>
  </h4>
</a>
</div>
</div>