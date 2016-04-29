<?php
function register_connect_documents() {
	add_submenu_page('edit.php?post_type=document', 'Przypisz dokumenty', 'Przypisz dokumenty', 'manage_options', 'document-connect', 'oz_connect_documents');
}
add_action('admin_menu', 'register_connect_documents');

function oz_connect_documents_enqueue() {
	if (isset($_GET['page'])) :
		if($_GET['page']=='document-connect') {
	        wp_enqueue_style( "oz-admin-css", get_stylesheet_directory_uri() . '/stylesheets/admin.css');
	        wp_enqueue_style( "oz-data-tables-css", get_stylesheet_directory_uri() . '/stylesheets/jquery.dataTables.css');
	        wp_enqueue_script('oz-data-tables-js', get_stylesheet_directory_uri() . '/javascripts/vendor/jquery.dataTables.min.js');
	        wp_enqueue_script('oz-connect-documents-admin', get_stylesheet_directory_uri() . '/options/js/connect-documents.js');
	        wp_localize_script( 'oz-connect-documents-admin', 'otwarte_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'template_url' => get_template_directory_uri() ) );
	    }
	endif;
}
add_action( 'admin_enqueue_scripts', 'oz_connect_documents_enqueue', 11 );

function oz_connect_documents() {
	?>
	<div class="wrap">
		<div id="icon-edit" class="icon32 icon32-posts-monument"><br></div>
		<h2><?php _e('Przypisz dokumenty do zabytków', 'otwarte2013'); ?></h2>
		<table class="form-table">
			<tr>
				<th>Wybierz zabytek:</th>
				<td>
					<select name="monument" id="monument">
						<option value="">--- Wybierz zabytek ---</option>
						<?php 
							$monuments_args = array('post_type' => 'monument', 'posts_per_page' => -1 );
							$monuments_query = new WP_Query($monuments_args);

							while($monuments_query->have_posts()) {
								$monuments_query->the_post();
								if($monuments_query->post->ID==$_GET['monument_id'])
									echo '<option value="'.$monuments_query->post->ID.'" selected="selected">'.get_the_title().'</option>';
								else
									echo '<option value="'.$monuments_query->post->ID.'">'.get_the_title().'</option>';
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Aktualnie przypisane dokumenty:</th>
				<td id="connected-documents"></td>
			</tr>
			<tr>
				<th>Wybierz dokumenty:</th>
				<td>
					<table id="documents-list">
						<thead>
							<tr>
								<th>Nazwa</th>
								<th>Sygnatura</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$e=1;
							$p=1;
							while ($e!=0) {
								$e=0;
								$documents_args = array('post_type' => 'document', 'posts_per_page' => 300, 'paged' => $p);
								$documents_query = new WP_Query($documents_args);

								while($documents_query->have_posts()) {
									$documents_query->the_post();
									$e++;
									?>
									<tr>
										<td><?php the_title(); ?></td>
										<td><?php echo get_post_meta($documents_query->post->ID, 'oz_document_signature', true); ?><div class="connect" data-id="<?php echo $documents_query->post->ID;?>">dodaj</div></td>
									</tr>
									<?php
								}
								$p++;
							}
							?>
						</tbody>
					</table>
				</td>
			</tr>
		</table>

	</div>
	<?php
}

if(is_admin()) {
	add_action('wp_ajax_get_documents_list', 'otwarte_get_documents_list');
	add_action('wp_ajax_nopriv_get_documents_list', 'otwarte_get_documents_list');
}

function otwarte_get_documents_list() {
	$monument_id = $_POST['monument_id'];
	$documents = json_decode(get_post_meta($monument_id, 'oz_documents', true));

	if($documents==null)
		$documents=array();
	$i=0;
	foreach($documents as $document) {
		$docs[$i]['title']=get_the_title($document);
		$docs[$i]['id']=$document;
		$i++;
	}

	echo json_encode($docs);
	die();
}

if(is_admin()) {
	add_action('wp_ajax_disconnect', 'otwarte_disconnect');
	add_action('wp_ajax_nopriv_disconnect', 'otwarte_disconnect');
}

function otwarte_disconnect() {
	$monument_id = $_POST['monument_id'];
	$document_id = $_POST['document_id'];

	disconnect_mon_doc($monument_id, $document_id);
	die();
}

if(is_admin()) {
	add_action('wp_ajax_connect', 'otwarte_connect');
	add_action('wp_ajax_nopriv_connect', 'otwarte_connect');
}

function otwarte_connect() {
	$monument_id = $_POST['monument_id'];
	$document_id = $_POST['document_id'];

	connect_mon_doc($monument_id, $document_id);
	$doc['id']=$document_id;
	$doc['title']=get_the_title($document_id);

	echo json_encode($doc);
	die();
}