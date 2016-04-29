<?php
function oz_create_documents() {
	$labels = array(
		'name'               => __('Dokumenty', 'otwarte2013'),
	    'singular_name'      => __('Dokument', 'otwarte2013'),
	    'add_new'            => __('Dodaj nowy', 'otwarte2013'),
	    'add_new_item'       => __('Dodaj nowy dokument', 'otwarte2013'),
	    'edit_item'          => __('Edytuj dokument', 'otwarte2013'),
	    'new_item'           => __('Nowy dokument', 'otwarte2013'),
	    'all_items'          => __('Wszystkie dokumenty', 'otwarte2013'),
	    'view_item'          => __('Zobacz dokument', 'otwarte2013'),
	    'search_items'       => __('Szukaj dokumentów', 'otwarte2013'),
	    'not_found'          => __('Nie znaleziono dokumentów', 'otwarte2013'),
	    'not_found_in_trash' => __('Nie znaleziono dokumentów w koszu', 'otwarte2013'),
	    'parent_item_colon'  => __('', 'otwarte2013'),
	    'menu_name'          => __('Dokumenty', 'otwarte2013'),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'dokument' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'menu_icon'			 => get_template_directory_uri().'/img/doc-ico-admin.png',
		//'capabilities'		 => array( 'create_posts' =>  false),
		'supports'           => array( 'title', 'thumbnail', 'custom-fields')
	);

  register_post_type( 'document', $args );
}
add_action('init', 'oz_create_documents');


function oz_document_add_metabox() {
	add_meta_box('oz_document_fields_metabox', __('Metadane', 'otwarte2013'), 'oz_document_fields_metabox', 'document', 'normal', 'high');
	add_meta_box('oz_document_files_metabox', __('Pliki', 'otwarte2013'), 'oz_document_files_metabox', 'document', 'normal', 'high');
}
add_action('add_meta_boxes', 'oz_document_add_metabox');

function oz_document_fields_metabox($post) {
	$keys = array(	'oz_document_signature',
					'oz_document_type',
					'oz_document_creator',
					'oz_document_sponsor',
					'oz_document_date',
					'oz_document_description',
					'oz_document_rights',
					'oz_document_source',
					'oz_document_city',
					'oz_monuments'
					);

	for($i=0; $i<sizeof($keys); $i++) {
		$values[$keys[$i]]=get_post_meta($post->ID, $keys[$i], true);
	}
	?>
	<table style="width: 100%;">
		<tr>
			<td><strong><?php _e('Sygnatura', 'otwarte2013') ?></strong></td>
			<td><input class="document-metadata" type="text" name="oz_document_signature" value="<?php echo $values['oz_document_signature']; ?>"></td>
		</tr>
		<tr>
			<td><strong><?php _e('Data dokumentu', 'otwarte2013') ?></strong></td>
			<td><input class="document-metadata" type="text" name="oz_document_date" value="<?php echo $values['oz_document_date']; ?>"></td>
		</tr>
		<tr>
			<td><strong><?php _e('Typ publikacji', 'otwarte2013') ?></strong></td>
			<td><input class="document-metadata" type="text" name="oz_document_type" value="<?php echo $values['oz_document_type']; ?>"></td>
		</tr>
		<tr>
			<td><strong><?php _e('Twórca', 'otwarte2013') ?></strong></td>
			<td><input class="document-metadata" type="text" name="oz_document_creator" value="<?php echo $values['oz_document_creator']; ?>"></td>
		</tr>
		<tr>
			<td><strong><?php _e('Projekt/sponsor digitalizacji', 'otwarte2013') ?></strong></td>
			<td><input class="document-metadata" type="text" name="oz_document_sponsor" value="<?php echo $values['oz_document_sponsor']; ?>"></td>
		</tr>
		<tr>
			<td><strong><?php _e('Udostępnianie publikacji cyfrowej', 'otwarte2013') ?></strong></td>
			<td><input class="document-metadata" type="text" name="oz_document_rights" value="<?php echo $values['oz_document_rights']; ?>"></td>
		</tr>
		<tr>
			<td><strong><?php _e('Dokument pochodzi z', 'otwarte2013') ?></strong></td>
			<td><input class="document-metadata" type="text" name="oz_document_source" value="<?php echo $values['oz_document_source']; ?>"></td>
		</tr>
		<tr>
			<td><strong><?php _e('Miejscowość', 'otwarte2013') ?></strong></td>
			<td><input class="document-metadata" type="text" name="oz_document_city" value="<?php echo $values['oz_document_city']; ?>"></td>
		</tr>
		<tr>
			<td><strong><?php _e('Powiązane zabytki', 'otwarte2013') ?></strong></td>
			<td>				
				<?php 
				$monuments = json_decode($values['oz_monuments']);
				if(sizeof($monuments)>0){
					echo '<ul>';
					foreach ($monuments as $monument) {
						echo '<li>';
						edit_post_link(get_the_title($monument), '', '', $monument );
						echo '</li>';
					}
					echo '</ul>';
				}
				echo '<a class="button" href="'.admin_url( 'edit.php?post_type=document&page=document-connect' ).'">'.__('Edytuj połączenia', 'otwarte2013').'</a>'; 
				?>
			</td>
		</tr>
	</table>
	<hr>
	<strong><?php _e('Opis dokumentu', 'otwarte2013') ?></strong>
	<?php
			$content = $values['oz_document_description'];
			$editor_id = 'oz_document_description';
			$settings = array( 'media_buttons' => false );

			wp_editor( $content, $editor_id, $settings);
			?>
	<?php
}

function oz_document_files_metabox($post) {
	$keys = array(	'oz_jpegs',
					'oz_pdf',
					'oz_djvu'
					);

	for($i=0; $i<sizeof($keys); $i++) {
		$values[$keys[$i]]=get_post_meta($post->ID, $keys[$i], true);
	}
	?>
	<p><strong><?php _e('Pliki', 'otwarte2013'); ?> JPG</strong></p>
	<div id="jpeg-list" class="clearfix">
		<?php 
		$jpegs = json_decode($values['oz_jpegs']); 
		if(sizeof($jpegs)>0) {
			foreach ($jpegs as $jpeg) {
				?>
				<div class="jpeg" data-id="<?php echo $jpeg; ?>">
				<?php echo wp_get_attachment_image( $jpeg, 'default-thumb'); ?>
				<?php edit_post_link( '', '', '', $jpeg ); ?>
				
				<a href="" class="delete"></a>
				</div>

				<?php
			}
		}
		?>
	</div>
	<button data-type="jpeg" class="add-file button">Dodaj</button>
	<hr>
	<p><strong><?php _e('Plik', 'otwarte2013'); ?> PDF</strong></p>
	<div id="pdf-list" data-id="<?php echo $values['oz_pdf']; ?>">
		<?php 
		edit_post_link(get_the_title($values['oz_pdf']), '', '', $values['oz_pdf'] );
		 ?>
	</div>
	<button data-type="pdf" class="add-file button">Dodaj</button><button data-type="pdf" class="delete-doc button">Usuń</button>
	<hr>
	<p><strong><?php _e('Plik', 'otwarte2013'); ?> DjVu (<?php _e('skompresowany folder w formacie ZIP', 'otwarte2013'); ?>)</strong></p>
	<div id="djvu-list" data-id="<?php echo $values['oz_djvu']; ?>">
		<?php 
		edit_post_link(get_the_title($values['oz_djvu']), '', '', $values['oz_djvu'] );
		 ?>
	</div>
	<button data-type="djvu" class="add-file button">Dodaj</button><button data-type="djvu" class="delete-doc button">Usuń</button>
	<br/><input type="text" name="oz_jpegs" id="oz_jpegs" value="<?php echo $values['oz_jpegs']; ?>">
	<input type="text" name="oz_pdf" id="oz_pdf" value="<?php echo $values['oz_pdf']; ?>">
	<input type="text" name="oz_djvu" id="oz_djvu" value="<?php echo $values['oz_djvu']; ?>">
	<?php
	echo '<input type="hidden" name="oz_document_noncename" id="oz_document_noncename" value="'.wp_create_nonce(plugin_basename(__FILE__).$post->ID).'" />';
}

function oz_document_save_postdata($post_id) {
	global $post;
	if (!wp_verify_nonce($_POST['oz_document_noncename'], plugin_basename(__FILE__).$post->ID)) {
    	return $post->ID;
 	}

	$keys = array(	'oz_document_signature',
					'oz_document_type',
					'oz_document_creator',
					'oz_document_sponsor',
					'oz_document_date',
					'oz_document_description',
					'oz_document_rights',
					'oz_document_source',
					'oz_document_city',
					'oz_jpegs',
					'oz_pdf',
					'oz_djvu');

	for($i=0; $i<sizeof($keys); $i++) {
		update_post_meta($post_id, $keys[$i], $_POST[$keys[$i]]);
	}
}
add_action( 'save_post', 'oz_document_save_postdata');



function oz_document_enqueue() {
	global $post_type;
    if( 'document' == $post_type ) {
    	
    	
    	wp_enqueue_script('oz-document-admin', get_stylesheet_directory_uri() . '/post-types/js/document.js');
    	wp_enqueue_style( "oz-admin-css", get_stylesheet_directory_uri() . '/stylesheets/admin.css');
    }
}
add_action( 'admin_enqueue_scripts', 'oz_document_enqueue', 11 );

function document_type() {

	$labels = array(
		'name'                       => _x( 'Typy dokumentów', 'Taxonomy General Name', 'otwarte2013' ),
		'singular_name'              => _x( 'Typ dokumentu', 'Taxonomy Singular Name', 'otwarte2013' ),
		'menu_name'                  => __( 'Typy dokumentów', 'otwarte2013' ),
		'all_items'                  => __( 'Wszystkie typy', 'otwarte2013' ),
		'parent_item'                => __( 'Nadrzędny typ', 'otwarte2013' ),
		'parent_item_colon'          => __( 'Nadrzędny typ:', 'otwarte2013' ),
		'new_item_name'              => __( 'Nowy typ dokumentu', 'otwarte2013' ),
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
	register_taxonomy( 'document_type', array( 'document' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'document_type', 0 );

?>

