<?php // single catalogue thumbnail 
  global $fol, $id;
?>
<div class="large-2 columns sf-cnt">
	<div class="top-border"> </div>
  <div class="single-folder" data-post-in="<?php echo 

$fol->ids?>" data-folder-id="<?php echo 

$id?>" >
  	<span class="rem-folder ico"> </span>
    <a href="#<?php echo 

$fol->name?>" class="<?php echo 

$fol->cover?>">
          <?
            $mobileThumb = wp_get_attachment_thumb_url ( $fol->cover ) ;
             $c_args = array(
              "data-interchange" => '[' . $mobileThumb .', ((min-width: 400px) and (max-width:768px)]',
              'class' => 'selected'
            );
            print( wp_get_attachment_image($fol->cover, 'cover-size', false, $c_args) );
          ?>
	</a>
</div>
	<h6 class="label">
          <span class="title-text"> <?php echo 

$fol->name?> </span>
    </h6>
</div>