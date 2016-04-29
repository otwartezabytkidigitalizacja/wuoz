<!DOCTYPE html>
<!--[if IE 8]><html class="no-js lt-ie9" <?php language_attributes(); ?> > <![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" <?php language_attributes(); ?> > <!--<![endif]-->

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title><?php wp_title(); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class("cbp-spmenu-push"); ?>>
<nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right" id="cbp-spmenu-s2">
  <h3><?php _e("Menu", "otwarte2013") ?></h3>
  <?php wp_nav_menu(array(
      'theme_location' => 'top-right',
      'menu_class' => 'inline-list right',
      'container' => false
  ));
  ?>
</nav>
  <section id="header-section">
    <div class="fixed" id="header-menu">
      <nav class="top-bar">
        <div class="row">
          <div class="large-12 columns">
            <div id="top-right" class="large-12 columns right show-for-medium-up">
             <div class="button-group right">
              <ul class="social inline-list right">
                <?php
                $fb = get_option('oz_facebook');
                $tw = get_option('oz_twitter');
                $vi = get_option('oz_vimeo');

                if($fb!='') {
                    ?>
                    <li> <a href="http://facebook.com" rel="nofollow" target="_blank" class="fb"> f </a> </li>
                    <?php
                  }
                if($tw!='') {
                    ?>
                    <li> <a href="http://twitter.com" rel="nofollow" target="_blank" class="tw"> tw </a> </li>
                    <?php
                  }
                  if($vi!='') {
                    ?>
                    <li> <a href="http://vimeo.com" rel="nofollow" target="_blank" class="vim"> v </a> </li>
                    <?php
                  }
                  ?>
              </ul>
              <?php wp_nav_menu(array(
                'theme_location' => 'top-right',
                'menu_class' => 'inline-list right',
                'container' => false
                ));
                ?>
              </div>
            </div>
          </div>
        </div>

        <div class="row">


          <div class="large-12 columns">
            <div class="log-cnt left large-4 columns" >
              <a id="logo" href="/"></a>
              <span id="showRightPush" class="hide-for-medium-up right"></span>
            </div>
            <?php if (get_option("oz_search_page") == $post->ID) :  ?>
              <form id="keyword_search">
              <p id="header-search" class="large-4 columns oz-button dark-green-bg searchpage">
                <span data-tooltip aria-haspopup="true" class="has-tip [tip-bottom]" title="Wpisz nazwę zabytku lub dokumentu">
                  <input type="search" name="searchword" id="searchword" placeholder="<?php _e('Szukaj', 'otwarte2013') ?>"/>
                </span>
                <span class="ico search-ico right"> </span>
              </p>
              </form>
            <?php else:  ?>
            <a id="header-search" href="<?php echo get_search_link(); ?>" class="large-4 columns oz-button dark-green-bg"> <?php _e('Przeglądaj zasoby', 'otwarte2013') ?>
              <span class="ico search-ico right"> </span>
            </a>
            <?php endif; ?>
            <?php get_template_part( 'partials/user', 'menu' ); ?>
          </div>
        </div>
      </div>
    </nav>
  </section>
