<?php
function oz_monument_create() {
	$labels = array(
		'name'               => __('Zabytki', 'otwarte2013'),
	    'singular_name'      => __('Zabytek', 'otwarte2013'),
	    'add_new'            => __('Dodaj nowy', 'otwarte2013'),
	    'add_new_item'       => __('Dodaj nowy zabytek', 'otwarte2013'),
	    'edit_item'          => __('Edytuj zabytek', 'otwarte2013'),
	    'new_item'           => __('Nowy zabytek', 'otwarte2013'),
	    'all_items'          => __('Wszystkie zabytki', 'otwarte2013'),
	    'view_item'          => __('Zobacz zabytek', 'otwarte2013'),
	    'search_items'       => __('Szukaj zabytków', 'otwarte2013'),
	    'not_found'          => __('Nie znaleziono zabytków', 'otwarte2013'),
	    'not_found_in_trash' => __('Nie znaleziono zabytków w koszu', 'otwarte2013'),
	    'parent_item_colon'  => __('', 'otwarte2013'),
	    'menu_name'          => __('Zabytki', 'otwarte2013')
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'zabytek' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'menu_icon'			 => get_template_directory_uri().'/img/mon-ico-admin.png',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields')
	);

  register_post_type( 'monument', $args );
}
add_action('init', 'oz_monument_create');

function oz_monument_add_metabox() {
	add_meta_box('oz_monument_map_metabox', __('Lokalizacja zabytku', 'otwarte2013'), 'oz_monument_map_inner_metabox', 'monument', 'normal', 'high');
	add_meta_box('oz_monument_meta_metabox', __('Metadane', 'otwarte2013'), 'oz_monument_meta_inner_metabox', 'monument', 'side', 'default');
	$screen = get_current_screen();
	if($screen->post_type=="monument" && $screen->action!='add')
		add_meta_box('oz_monument_documents_metabox',  __('Dokumenty', 'otwarte2013'), 'oz_monument_documents_inner_metabox', 'monument', 'side', 'default');
}
add_action('add_meta_boxes', 'oz_monument_add_metabox');

function oz_monument_documents_inner_metabox($post) {
	$keys = array(	'oz_documents');

	for($i=0; $i<sizeof($keys); $i++) {
		$values[$keys[$i]]=get_post_meta($post->ID, $keys[$i], true);
	}
	?>
	<strong><?php _e('Powiązane dokumenty', 'otwarte2013') ?></strong><br/>
	<?php 
	$documents = json_decode($values['oz_documents']);
	
	if(sizeof($documents)>0) {
		echo '<ul>';
		foreach($documents as $document) {
			echo '<li>';
			edit_post_link(get_the_title($document), '', '', $document );
			echo '</li>';
			
		}
		echo '</ul>';
	} 
	echo '<a class="button" href="'.admin_url( 'edit.php?post_type=document&page=document-connect&monument_id='.$post->ID  ).'">'.__('Edytuj połączenia', 'otwarte2013').'</a>';
	echo '<a class="button" href="'.admin_url( 'edit.php?post_type=document&page=document-add-multuple&monument_id='.$post->ID ).'">'.__('Dodaj dokumenty', 'otwarte2013').'</a>';
}

function oz_monument_map_inner_metabox($post) {
	$keys = array(	'oz_city',
					'oz_address',
					'oz_region',
					'oz_lat',
					'oz_lng',
					'oz_year',
					'oz_organization');

	for($i=0; $i<sizeof($keys); $i++) {
		$values[$keys[$i]]=get_post_meta($post->ID, $keys[$i], true);
	}
	?>
	<div class="location-fields">
		<span class="step">1</span> <strong><?php _e('Miejscowość', 'otwarte2013') ?></strong><br/>
		<input name="oz_city" id="oz_city" type="text" value="<?php echo $values['oz_city']; ?>"><br/><br/>

		<span class="step">2</span> <strong><?php _e('Adres', 'otwarte2013') ?></strong><br/>
		<input name="oz_address" id="oz_address" type="text"  value="<?php echo $values['oz_address']; ?>"><br/><br/>

		<span class="step">3</span> <strong><?php _e('Województwo', 'otwarte2013') ?></strong><br/>
		<input name="oz_region" id="region" type="text" value="<?php echo $values['oz_region']; ?>"><br/><br/>

		<input type="button" id="update_map_from_address_button" class="button" value="Znajdź na mapie"></input><br/><br/>

		<strong><?php _e('Współrzędne', 'otwarte2013') ?></strong> (użyj mapy)<br/>
		<input name="oz_human_lat" id="oz_human_lat" type="text" disabled="disabled"  value="<?php echo get_human_lat($values['oz_lat']); ?>">
		<input name="oz_human_lng" id="oz_human_lng" type="text" disabled="disabled" value="<?php echo get_human_lng($values['oz_lng']); ?>">
		<br><br>
		<?php _e('Współrzędne w formacie Google', 'otwarte2013') ?><br>
		<input name="oz_lat" id="oz_lat" type="text" value="<?php echo $values['oz_lat']; ?>">
		<input name="oz_lng" id="oz_lng" type="text"  value="<?php echo $values['oz_lng']; ?>">
		<input type="button" id="update_map_from_coords_button" class="button" value="Znajdź na mapie"></input><br/><br/>
	</div>
	<div class="location-map">
		<span class="step">4</span> <strong><?php _e('Przeciągnij marker, aby dostosować lokalizację i współrzędne obiektu. Adres nie zostanie zmieniony.', 'otwarte2013') ?></strong><br/><br/>
		<div id="monument-map" style="width: 100%; height: 400px;"></div>
	</div>
	<?php
}

function oz_monument_meta_inner_metabox($post) {
	$keys = array(	'oz_city',
					'oz_address',
					'oz_region',
					'oz_lat',
					'oz_lng',
					'oz_year',
					'oz_organization',
					'oz_documents');

	for($i=0; $i<sizeof($keys); $i++) {
		$values[$keys[$i]]=get_post_meta($post->ID, $keys[$i], true);
	}
	?>
	<strong><?php _e('Rok powstania', 'otwarte2013') ?></strong><br/>
	<input name="oz_year" id="oz_year" type="text" value="<?php echo $values['oz_year']; ?>"><br/><br/>

	<strong><?php _e('Przynależność administracyjna', 'otwarte2013') ?></strong><br/>
	<input name="oz_organization" id="oz_organization" type="text" value="<?php echo $values['oz_organization']; ?>"><br/><br/>
	
	<?php
	echo '<input type="hidden" name="oz_monument_noncename" id="oz_monument_noncename" value="'.wp_create_nonce(plugin_basename(__FILE__).$post->ID).'" />';
}

function oz_monument_save_postdata($post_id) {
	global $post;
	if (!wp_verify_nonce($_POST['oz_monument_noncename'], plugin_basename(__FILE__).$post->ID)) {
    	return $post->ID;
 	}

	$keys = array(	'oz_city',
					'oz_address',
					'oz_region',
					'oz_lat',
					'oz_lng',
					'oz_year',
					'oz_organization',
					);

	for($i=0; $i<sizeof($keys); $i++) {
		update_post_meta($post_id, $keys[$i], $_POST[$keys[$i]]);
	}
}
add_action( 'save_post', 'oz_monument_save_postdata');


function oz_monument_enqueue() {
	global $post_type;
    if( 'monument' == $post_type ) {
    	wp_enqueue_script('oz-monument-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.get_maps_api_key().'&sensor=false');
    	wp_enqueue_script('oz-monument-gmap3', get_stylesheet_directory_uri() . '/javascripts/vendor/gmap3.min.js');
    	wp_enqueue_script('oz-monument-admin', get_stylesheet_directory_uri() . '/post-types/js/monument.js');
    	wp_enqueue_style( "oz-admin-css", get_stylesheet_directory_uri() . '/stylesheets/admin.css');
    }
}
add_action( 'admin_enqueue_scripts', 'oz_monument_enqueue', 11 );

function monument_type() {

	$labels = array(
		'name'                       => _x( 'Typy zabytków', 'Taxonomy General Name', 'otwarte2013' ),
		'singular_name'              => _x( 'Typ zabytku', 'Taxonomy Singular Name', 'otwarte2013' ),
		'menu_name'                  => __( 'Typy zabytków', 'otwarte2013' ),
		'all_items'                  => __( 'Wszystkie typy', 'otwarte2013' ),
		'parent_item'                => __( 'Nadrzędny typ', 'otwarte2013' ),
		'parent_item_colon'          => __( 'Nadrzędny typ:', 'otwarte2013' ),
		'new_item_name'              => __( 'Nowy typ zabytku', 'otwarte2013' ),
		'add_new_item'               => __( 'Dodaj nowy typ', 'otwarte2013' ),
		'edit_item'                  => __( 'Edytuj typ', 'otwarte2013' ),
		'update_item'                => __( 'Zaktualizuj typ', 'otwarte2013' ),
		'separate_items_with_commas' => __( 'Oddziel wartości przecinkami', 'otwarte2013' ),
		'search_items'               => __( 'Szukaj typów', 'otwarte2013' ),
		'add_or_remove_items'        => __( 'Dodaj lub usuń typy', 'otwarte2013' ),
		'choose_from_most_used'      => __( 'Wybierz z najczęściej używanych', 'otwarte2013' ),
		'not_found'                  => __( 'Nie znaleziono', 'otwarte2013' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'monument_type', array( 'monument' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'monument_type', 0 );
