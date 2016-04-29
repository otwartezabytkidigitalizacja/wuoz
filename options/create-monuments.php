<?php
function register_create_monuments() {
	add_submenu_page('edit.php?post_type=monument', 'Stwórz zabytki', 'Stwórz zabytki', 'manage_options', 'create-monuments', 'oz_create_monuments');
}
add_action('admin_menu', 'register_create_monuments');

function oz_create_monuments_enqueue() {
	if (isset($_GET['page'])) :
		if($_GET['page']=='create-monuments') {
	        wp_enqueue_style( "oz-admin-css", get_stylesheet_directory_uri() . '/stylesheets/admin.css');
	    }
	endif;
}
add_action( 'admin_enqueue_scripts', 'oz_create_monuments_enqueue', 11 );

function oz_create_monuments() {
	$remove_words = array(
		'dom',
		'mieszkalny',
		'budynki',
		'przy',
		'zabudowa',
		'budynek',
		'domy',
		'kapliczka',
		'szkoła',
		'nr',
		' - ',
		'apteka'
		);

	// ul. pl. Rynek
	?>
	<div class="wrap">
		<div id="icon-edit" class="icon32 icon32-posts-monument"><br></div>
		<h2><?php _e('Stwórz zabytki', 'otwarte2013'); ?></h2>
		<?php
		$xml=simplexml_load_file(get_template_directory() . '/options/zab.xml');
		
		// $last_monument = 0;
		$last_monument_name = '';
		$i = -1;
		foreach($xml->children() as $child) {
			if($child->processed) {
				continue;
			}
			if($child->nazwa_zabytku) {
				$name = $child->nazwa_zabytku.'';
				
				if($name!=$last_monument_name) {
					$last_monument_name=$name;

					$i++;
					if($i>99)
						break;

					
					$address = $child->adres.'';
					$city = $child->miejscowosc.'';
					$region = 'opolskie';

					$monument_args = array(
						'post_title' => $name,
						'post_type' => 'monument',
						'post_status' => 'publish'
						);

					$monument_id = wp_insert_post($monument_args);
					//$last_monument = $monument_id;

					$ask_google = false;
					
					if($address!='') {
						update_post_meta($monument_id, 'oz_address', $address);
						$google_string = $address.', ';
						$ask_google = true;
					}
						
					if($city!='' && $city!='BRAK DANYCH') {
						update_post_meta($monument_id, 'oz_city', $city);
						$google_string .= $city .', ';
						$ask_google = true;
					}

					if($city=='BRAK DANYCH')
						update_post_meta($monument_id, 'oz_city', $city);

					update_post_meta($monument_id, 'oz_region', $region);

						
					if($ask_google) {
						$link = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($google_string.'opolskie, polska').'&sensor=false';
						$output = json_decode(file_get_contents($link));
						$location = $output->results[0]->geometry->location;
						update_post_meta($monument_id, 'oz_lat', $location->lat);
						update_post_meta($monument_id, 'oz_lng', $location->lng);
					}

					echo '<br>--------------------------------------------------------<br><br><b>'.$i.'. '.$child->nazwa_zabytku.'</b><br><br>';
				}
			}

			$signature = $child->sygnatura.'';

			$doc_args = array (
				'post_type' => 'document',
				'post_status' => 'draft',
				'meta_query' => array(
					array(
						'key'       => 'oz_document_signature',
						'value'     => $signature,
					),
				),
			);

			$documents = get_posts( $doc_args );

			//echo '<br>'.$signature.'<br>';
			foreach($documents as $document) {
				connect_mon_doc($monument_id, $document->ID);
				$up_post = array(
					'ID' => $document->ID,
					'post_status' => 'publish'
				);
				wp_update_post($up_post);
				echo $monument_id.' <-> '.$document->ID.'<br>';
			}	
			if(sizeof($documents)>1) {
				echo '<b>Uwaga - zdublowana sygnatura!</b> <br>Zabytek: '.$name.'<br>Sygnatura: '.$signature.'<br>';
				echo 'Dokumenty: <Br>';
				foreach($documents as $document) {
					echo $document->post_title.'<br>';
				}
				echo '<br>';
			}

			$child->addChild('processed');
		}

		$xml -> asXML(get_template_directory() . '/options/zab.xml');

		//print_r($xml);

		?>
	</div>
	<?php
}