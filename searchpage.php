<?php
/*
Template Name: Search Page
*/
?>

<?php get_header(); ?>

<section id ="filter-grey" class="first-top-container">
  <div class="row">
    <div id="recent-label" class="large-12 columns">

      <div class="large-3 columns object-filter">
        <span class="slabel"> <?php _e('pokaż', 'otwarte2013')?> </span>
        <a href="#dokumenty"  data-type="document">
          <span class="ico doc-ico"> </span>
          <span class="caps"> <?php _e('dokumenty', 'otwarte2013')?>
          </a>
          <a href="#zabytki"  data-type="monument">
            <span class="ico mon-ico"> </span>
            <span class="caps"> <?php _e('zabytki', 'otwarte2013')?>
            </a>
          </div>
          <div class="large-4 columns category-info">
            <p><?php _e("&lArr;  Filtry zostaną włączone po wyborze typu obiektów", 'otwarte2013'); ?></p>
          </div>
          <div class="large-4 columns document-types">
            <?php $doc_types = get_terms( 'document_type', 'orderby=name' ); ?>
            <select class="filter-select" name="" id="document-type" multiple="multiple" placeholder="<?php _e("Wybierz kategorię", "otwarte2013") ?>">

              <?php foreach($doc_types as $doc_type) { ?>
                <option value="<?php echo $doc_type->slug?>"><?php echo $doc_type->name ?></option>
                <?php }; ?>
            </select>
          </div>
      <div class="large-4 columns monument-types">
        <?php
        $tax = array(
            'monument_type'
        );
        $args = array(
            'orderby' => 'name',
            'hide_empty' => false
        );

        $mon_types = get_terms($tax, $args );
        ?>
        <select class="filter-select" name="" id="monument-type" multiple="multiple" placeholder="<?php _e("Wybierz kategorię", "otwarte2013") ?>">

          <?php foreach($mon_types as $mon_type) { ?>
            <option value="<?php echo $mon_type->slug?>"><?php echo $mon_type->name ?></option>
          <?php } ?>

        </select>
      </div>
          <div class="large-5 columns object-sorters">
            <span class="slabel"> <?php _e('sortuj', 'otwarte2013')?> </span>

            <a href="#nazwa" class="ajax-on" data-order="ASC" data-order-by="name">
              <span class="caps"> <?php _e('nazwa', 'otwarte2013')?>
                <span class="ico ar-sort"> </span>
              </a>

              <a href="#data-powstania" class="ajax-off" data-order="DESC" data-order-by="meta_value">
                <span class="caps"> <?php _e('data powstania', 'otwarte2013')?>
                  <span class="ico ar-sort"> </span>
                </a>
                <a href="#data-oddania" class="ajax-off" data-order="DESC" data-order-by="date">
                  <span class="caps"> <?php _e('data dodania', 'otwarte2013')?>
                    <span class="ico ar-sort"> </span>
                  </a>

                </div>
              </div>
              <div data-alert class="alert-box" tabindex="0" aria-live="assertive" role="alertdialog" style="display:none">
                Zabytki bez określonej daty powstania zostały pominięte
                <button tabindex="0" class="close" aria-label="Close Alert">&times;</button>
              </div>
            </div>
          </section>
          <section id="search-results" class="search-base">
            <div id="ajax-wrapper" class="preloader">
              <?// echo_search_results() ?>
            </div>
          </section>
  <?php get_footer();
