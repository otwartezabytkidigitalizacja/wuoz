<?php // single catalogue thumbnail 
  $covers = json_decode(get_option('oz_folder_icons'));
  // echo '<pre>';
  // var_dump($covers);
  // echo '</pre>';

?>
<div class="large-2 columns add-cnt sf-cnt">
  <div id="add-folder-cnt" class="single-folder">

    <a data-reveal-id="addfolder-reveal">
          <img src="<?php echo 

get_template_directory_uri() . '/img/'?>add_folder_icon.png" alt="default_thumbnail"/>
    </a>
</div>
</div>
 
<div id="addfolder-reveal" class="reveal-modal small">
  <form name="addcover" id="add-folder" method="post" data-abide>
    <p>
      <input pattern="alpha_numeric" required id="folder-name" type="text" name="folder-name" placeholder="<?php _e('nazwa katalogu', 'otwarte2013')?>" />
      <span class="error"><?php _e('Nazwa katalogu jest wymagana i może składać się z cyfr i znaków', 'otwarte2013')?></span>
    </p>
    <p>
    <h6 class="caps"><?php _e('wybierz okładkę','otwarte2013'); ?> </h6>
    </p>
    <div class="cover-cnt row">
        <div class="large-12">
        <?php
          $count = 0;
        foreach($covers as $key => $cover) : 
           echo $count == 0 ? '<div class="large-6 left cover-sel" data-cover-id="' . $cover . '">' : '<div class="large-6 left" data-cover-id="' . $cover . '">';
            echo '<span class="ico"><span></span></span>';
             $mobileThumb = wp_get_attachment_thumb_url ( $cover ) ;
             $c_args = array(
              "data-interchange" => '[' . $mobileThumb .', ((min-width: 400px) and (max-width:768px)]',
              'class' => 'selected'
            );
            print( wp_get_attachment_image($cover, 'cover-size', false, $c_args) );
           echo '</div>';
           $count++;
           // echo $count % 2 == 0 ? '<div class="row"></div>' : '' ;
          endforeach; 
           ?>
          </div>
    </div>
    
    <a id="add-user-folder" class="wp-submit large-12 columns large-centered">
      <span><?php _e('Dodaj', 'otwarte2013')?> </span>
      <span class="ico go-ico right"> </span>
    </a>

    <input id="folder-cover" type="hidden" name="cover" value="<?php echo 

$covers[0]?>" />

  </form>
  <a class="close-reveal-modal ico"></a>
</div>