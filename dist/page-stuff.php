<?php
/*
Template Name: Nie ruszać :)
*/

get_header(); ?>
<section class="first-top-container" style="margin-top: 135px;">
	<div class="row">
		<div class="large-centered columns large-8">
			<?php 
			/*$i=0;
			for($j=1; $j<10; $j++) {
				$documents_args = array(
					'post_type' => 'document', 
					'posts_per_page' => 500,
					'paged' => $j,
					'post_status' => 'draft'
					);
				$documents_query = new WP_Query($documents_args);

				$document_types = array();
				$i=0;
				while($documents_query->have_posts()) {
					$documents_query->the_post();
					$type = get_post_meta($documents_query->post->ID, 'oz_document_type', true);
					if($type=='fotografia') {
						wp_set_object_terms($documents_query->post->ID, 'fotografia', 'document_type');
					}
					else if($type=='karta ewidencyjna') {
						$sig = substr(get_post_meta($documents_query->post->ID, 'oz_document_signature', true), 0, 2);
						if($sig=='KZ' || $sig=='kz'){
							wp_set_object_terms($documents_query->post->ID, 'karta-zielona', 'document_type');
						}
						else if($sig=='KB' || $sig=='kb') {
							wp_set_object_terms($documents_query->post->ID, 'karta-biala', 'document_type');
						}
					}
					else if($type=='rysunek techniczny') {
						wp_set_object_terms($documents_query->post->ID, 'rysunek-techniczny', 'document_type');
					}
					else if($type=='tekst') {
						wp_set_object_terms($documents_query->post->ID, 'tekst', 'document_type');
					}

				}
			}
			//print_r($document_types);*/
			if($_GET['pg']) {

				$documents_args = array(
						'post_type' => 'document', 
						'posts_per_page' => 500,
						'paged' => $_GET['pg'],
						//'paged' => $j,
						//'post_status' => 'draft'
						);
				$documents_query = new WP_Query($documents_args);
				echo '<strong>'.$_GET['pg'].'/'.$documents_query->max_num_pages.'</strong><br><Br><br>';

				while($documents_query->have_posts()) {
					$documents_query->the_post();
					the_title();
					echo ' - '.$documents_query->post->ID.'<br>';
					$djvu = get_post_meta($documents_query->post->ID, 'oz_djvu', true);
					$zip_name = get_attached_file($djvu, true);
					
					$zip = new ZipArchive;
					if($zip->open($zip_name)===TRUE) {
						for ($i = 0; $i < $zip->numFiles; $i++) {
					        echo 'old: ' . $zip->getNameIndex($i) . '<br />';
					        $newname = explode(DIRECTORY_SEPARATOR, $zip->getNameIndex($i));
					        $newname = $newname[sizeof($newname)-1];
					        echo 'new: '.$newname.'<br>';
					        $zip->renameIndex($i, $newname);

					        echo '--------------------------<br>';

					    }
					    $zip->close();
					}

					echo '<br>--------------------------------------------------------------------------<br /><br>';

				}
			}

			?>
		</div>
	</div>
</section>
<?php get_footer(); ?>