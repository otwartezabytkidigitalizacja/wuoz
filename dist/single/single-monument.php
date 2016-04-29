<?php
global $object, $post, $prev_id, $next_id;

if ( is_single() ) $object = $post;
$post_id = $object->ID;
$logged = is_user_logged_in();
$oz_meta = get_post_custom( $post_id );
echo '<pre>';

// $count = 0;
foreach ( $oz_meta as $key => $value ) {
	if ( strpos( $key, 'oz_' ) !== 0 ) {
		unset( $oz_meta[$key] );
	}
}

$labels = array (
	'oz_city' => __( 'Miejscowość', 'otwarte2013' ),
	'oz_address' => __( 'Adres', 'otwarte2013' ),
	'oz_region' => __( 'Województwo', 'otwarte2013' ),
	'oz_cord' => __( 'Szerokość', 'otwarte2013' ),
	'oz_year' => __( 'Data powstania', 'otwarte2013' ),
	'oz_organization' => __( 'Przynależność administracyjna', 'otwarte2013' ),
	'oz_cord' => __( 'Współrzędne', 'otwarte2013' )
);

// cook cords from lat ang lng

$tmp =  $oz_meta['oz_lng'][0];
$oz_meta['oz_lng'][0] = get_human_lng( $tmp );
$tmp =  $oz_meta['oz_lat'][0];
$oz_meta['oz_lat'][0] = get_human_lat( $tmp );

$cord = $oz_meta['oz_lat'][0] . ', ' . $oz_meta['oz_lng'][0];
//echo $cord;

unset( $oz_meta['oz_lat'] );
unset( $oz_meta['oz_lng'] );
$documents_id = json_decode($oz_meta['oz_documents'][0], true);

//var_dump($documents_id);

//echo 'po sortcie' . "\n";
is_array($documents_id) ? natsort($documents_id) : '' ;

//var_dump($documents_id);

$_POST['posts'] = $documents_id;
$_POST['orderby'] = 'ID';
$_POST['posts_per_page'] = -1;
unset( $oz_meta['oz_documents'] );
$oz_meta['oz_cord'][0] = $cord;

//var_dump( $post );
echo '</pre>';

?>

<section id="monument-controller" class="popup-controls" data-monument-id="<?php echo 

$post_id?>">
  <?php if ( !is_single() ) : ?>
   <?php if ( !empty( $prev_id ) ) : ?>
  <a id="object-prev" class="popup-nav arrow-prev-green" data-post-id="<?php echo $prev_id?>">
  	<span class="ico"></span>
  </a>
	<?php endif ?>
	<?php if ( !empty( $next_id ) ) : ?>
  <a id="object-next" class="popup-nav arrow-next-green" data-post-id="<?php echo $next_id?>">
  	<span class="ico"></span>
  </a>
	<?php endif; ?>
	<?php endif; ?>
	<div class="row">
		<div class="large-12 columns">
			<div class="button-group large-12 columns popup-info" data-post-id="<?php echo $post_id ?>">
				<a href="<?php echo is_single() ? get_permalink( get_option( 'oz_search_page' ) ) : '#' ;?>" class="close-popup <?php echo is_single() ? '' : 'close-reveal-modal' ;?>"><span class="ico "></span></a>
				<form method="post" action="<?php echo get_permalink(get_option('oz_download_page'));?>">
					<input type="hidden" name="id" value="<?php echo $post_id; ?>">
						<button class="oz-button right zip-cnt">
							<span class="ico zip-ico"></span>
							<span><?php _e( 'Pobierz dokumenty zabytku', 'otwarte2013' ) ?></span>
						</button>	
				</form>
				

				<?php if ( $logged ) : ?>

		          <?php if ( is_fav( $post_id ) ) : ?>
		            <a href="" class="oz-button right alreadyfav fav-cnt">
						<span class="ico fav-star-ico"></span>
						<span><?php _e( 'Odznacz', 'otwarte2013' ) ?></span>
					</a>
		          <?php else : ?>
		            <a href="" class="oz-button right fav-cnt">
						<span class="ico fav-star-ico"></span>
						<span><?php _e( 'Zaznacz', 'otwarte2013' ) ?></span>
					</a>
		          <?php endif?>
		      <?php endif ?>

			</div>
		</div>
	</div>
</section>
<section class="single-cnt">
  <div class="row">
  	<div class="top-section large-10 columns large-centered">
  		<div class="large-6 columns thumb-cnt">
			<?php echo get_the_post_thumbnail( $post_id, 'single-thumbnail' ); ?>
  		</div>
  		<div class="large-6 columns data-cnt">
  			<h5><span class="mon-ico ico"></span><?php echo $object->post_title ?></h5>
  			<table>
  				<?php foreach ( $oz_meta as $label => $value ) { 
  					if($value[0]!='') {?>
	  				<tr>
						<td><?php echo $labels[$label]?> </td>
						<td><?php echo $value[0]?> </td>
	  				</tr>
	  				<?php 
	  			}
  			} 
  			?>
  			</table>
  		</div>
  	</div>
  </div>
  <div class="row">
  	<div class="large-10 large-centered columns">
  		<div class="section-container accordion" data-section="accordion">
			  <section class="active">
			    <p class="title" data-section-title><a href="#"> <?php _e( 'Opis zabytku', 'otwarte2013' ) ?> </a></p>
			    <div class="content" data-section-content style="display: block">
			      <p><?php echo $object->post_content ?></p>
			    </div>
			  </section>
			  <section>
			    <p class="title" data-section-title><a href="#"> <?php _e( 'Mapa', 'otwarte2013' ) ?> </a></p>
			    <div class="content map-content" data-section-content>
			      <div id="monument-map" data-lat="<?php echo get_post_meta($post_id, 'oz_lat', true); ?>" data-lng="<?php echo get_post_meta($post_id, 'oz_lng', true); ?>">
			      	
			      </div>
			    </div>
			  </section>
		</div>
  	</div>
  </div>
  <div class="row">
  <div id="related-docs" class="large-12 columns" data-documents-ids="<?php echo 

implode($documents_id, ',')?>">
  <?php if (!empty($documents_id[0])) : ?>
		<h3> <?php _e( 'Dokumenty związane z zabytkiem', 'otwarte2013 ' )?></h3>
	<section id="object-results" class="search-base" >
            <div id="ajax-object-wrapper">

              <?php echo_search_results() ?>

            </div>
    </section>
<?php else :?>
	<h3> <?php _e( 'Zabytek nie ma przypisanych dokumentów', 'otwarte2013 ' )?></h3>
	<section id="object-results" class="search-base empty">
            <div id="ajax-object-wrapper"></div>
    </section>

<?php endif; ?>
  </div>
</div>