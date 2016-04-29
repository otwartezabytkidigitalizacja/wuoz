<?php get_header(); ?>
<!-- First Band (Slider) -->
<section id="slider" class="first-top-container">
  <div class="row">
    <div class="large-12 columns">
      <div id="slider-center-wrapper" class="large-10 large-centered columns" >
        <ul data-orbit data-options="animation: slide; slide_nubmer: false; bullets: false; timer: false; animation_speed: 500 ">
          <?php
          $slides = json_decode(stripcslashes(get_option('oz_slides')));
          foreach($slides as $slide) { ?>
          <li>
            <div class="slide-cnt columns large-centered">
              <?php
              if($slide->li_1!='' || $slide->li_2!='' || $slide->li_3!='') {
              ?>
                <div class="large-3 columns left"> <h6 class="caps"><?php echo $slide->text;?></h6> </div>
                 <ul class=" large-7 columns right">
                  <?php if($slide->li_1!='') { ?><li><?php echo $slide->li_1; ?></li><?php } ?>
                  <?php if($slide->li_2!='') { ?><li><?php echo $slide->li_2; ?></li><?php } ?>
                  <?php if($slide->li_3!='') { ?><li><?php echo $slide->li_3; ?></li><?php } ?>
                </ul>
              <?php
              } else {
                ?>
                <div> <h6><?php echo $slide->text;?></h6> </div>
                <?php
              }
              ?>



          </div>

        </li>
      <?php } ?>

    </ul>
    <div class="slider-video-cnt large-10 columns large-centered">
      <img src="<?php echo get_template_directory_uri() . '/img/slider_video_bg.png'?>" data-reveal-id="video-reveal" />
    </div>
  </div>
</div>
<div id="video-reveal" class="reveal-modal large">
    <div class="flex-video">
        <div class="preloader"> </div>
    </div>
    <a class="close-reveal-modal ico"></a>
</div>
</section>

<!-- Home Search Engine -->
<section id="home-search" class="green-div">
  <div class="row">
    <div class="large-12 columns">
      <a href="<?php echo

get_search_link(); ?>" class="large-4 columns large-centered oz-button dark-green-bg"> <?php _e('Przeglądaj zasoby', 'otwarte2013') ?>
        <span class="ico search-ico right"> </span>
      </a>
    </div>
  </div>
</section>

<div id="recent-label" class="row">
  <div class="large-12 columns">

    <h3> <?php _e('ostatnio dodane', 'otwarte2013')?>:
      <span class="caps"><a href="<?php echo get_permalink(get_option('oz_search_page')); ?>#dokumenty"><?php _e('dokumenty', 'otwarte2013')?></a>
        <span class="ico doc-ico"> </span>
        <span class="caps"><a href="<?php echo get_permalink(get_option('oz_search_page')); ?>#zabytki"><?php _e('zabytki', 'otwarte2013')?></a>
          <span class="ico mon-ico"> </span>
          <h3>

          </div>
        </div>
        <!-- Recent Monuments and Docs -->
        <div class="row objects-cnt">
              <?php
              $args = array(
                "post_type" => array('document', 'monument'),
                'posts_per_page' => 4
                );
              $otwarte_objects = new WP_Query($args);
              if( $otwarte_objects->have_posts() ):
              while( $otwarte_objects->have_posts() ): $otwarte_objects->the_post();
                get_template_part('partials/object', 'thumb');
              endwhile;
            endif;
              ?>
        </div>

        <section id="home-map-container">
          <div id="home-map-shadow"></div>
          <div id="home-map"></div>
        </section>


        <?php get_footer(); ?>
