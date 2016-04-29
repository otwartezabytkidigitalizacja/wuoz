 <!-- My Favourites -->
 <?
    global $post;
    (int) get_option('oz_my_catalogues_page') == $post->I<?php _e = true : $folders = false;
 ?>
 <div id="my-favs" class="hide-for-small <?php echo 

((isset($_COOKIE['open_favs']) && $_COOKIE['open_favs'] == 1) || $folders ) ? 'bar-opened' : '' ;?>">
  <div id="ver-bar-cnt" class="left">
    <div id="ver-bar">
      <span class="ico ar"> </span>
      <p class="rotate"> <?php _e('Moje zaznaczenia', 'otwarte2013'); ?></p>
      <span class="ico fav-star"> </span>
    </div>
  </div>
  <div id="list-cnt">
    <?php echo_user_favs() ?>
  </div>
</div>