<?php
function register_add_documents() {
	add_submenu_page('edit.php?post_type=document', 'Import dokumentów', 'Import dokumentów', 'manage_options', 'document-add-multuple', 'oz_add_documents');
}
add_action('admin_menu', 'register_add_documents');

function oz_add_documents_enqueue() {
	if (isset($_GET['page'])) :
		if($_GET['page']=='document-add-multuple') {
	        wp_enqueue_style( "oz-admin-css", get_stylesheet_directory_uri() . '/stylesheets/admin.css');
	        wp_enqueue_script('oz-add-documents-admin', get_stylesheet_directory_uri() . '/options/js/add-documents.js');
	        wp_localize_script( 'oz-add-documents-admin', 'otwarte_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'template_url' => get_template_directory_uri() ) );
	    }
	endif;
}
add_action( 'admin_enqueue_scripts', 'oz_add_documents_enqueue', 11 );

function oz_add_documents() {
	?>
	<div class="wrap">
		<div id="icon-edit" class="icon32 icon32-posts-monument"><br></div>
		<h2><?php _e('Import dokumentów', 'otwarte2013'); ?></h2>
		<?php //echo ABSPATH;
		$root = ABSPATH.'documents-upload';
		if (!file_exists($root))
			echo '<div id="message" class="error"><p><strong>Katalog importu dokumentów nie istnieje.</strong></p><p>'.__('Aby skorzystać ze narzędzia importu, utwórz folder o nazwie <strong>documents-upload</strong> w katalogu głównym WordPressa (').ABSPATH.'documents-upload).<br/>
					Po utworzeniu folderu, wyślij do niego przez FTP lub SFTP pliki dokumentów.</p>
					<p><strong>Wymagana stuktura plików:</strong></p>
					<p>/documents-upload<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/Dokumementacja_techniczna<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/SYGNATURA.DOKUMENTU<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/JPEG<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/PDF<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ALTO<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/TEXT<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/DjVu<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/Obiekt-WUOZ-XXXXX.xml<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/Foto<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/karty_biale<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/karty_zielone<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ksiazki<br/></p></div>';

		else {
			echo '<h3>'.__('Zabytek', 'otwarte2013').'</h3>';
			_e('Jeśli chcesz, aby importowane dokumenty zostały automatycznie przypisane do zabytku, wybierz go:', 'otwarte2013');
			?>
			<br><br>
			<select name="monument" id="monument">
				<option value="-1">--- <?php _e('Nie przypisuj', 'otwarte2013'); ?> ---</option>
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
			<hr>
			<?php
			$documents_count=0;
			$document_folders = array('JPEG', 'PDF', 'DjVu', 'TEXT', 'ALTO');
			$imported_file = '.imported';
		    $dir = new DirectoryIterator($root);
			foreach ($dir as $fileinfo) {
			    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
			        if(strcasecmp($fileinfo->getFilename(), 'Foto')==0) {
			        	echo '<h3>'.__('Zdjęcia', 'otwarte2013').'</h3>';
			        	$type = 'photo';
			        }
			        else if(strcasecmp($fileinfo->getFilename(), 'karty_biale')==0) {
			        	echo '<h3>'.__('Karty białe', 'otwarte2013').'</h3>';
			        	$type = 'white card';
			        }
			        else if(strcasecmp($fileinfo->getFilename(), 'karty_zielone')==0) {
			        	echo '<h3>'.__('Karty zielone', 'otwarte2013').'</h3>';
			        	$type = 'green card';
			        }
			        else if(strcasecmp($fileinfo->getFilename(), 'ksiazki')==0) {
			        	echo '<h3>'.__('Książki', 'otwarte2013').'</h3>';
			        	$type = 'book';
			        }
					else if(strcasecmp($fileinfo->getFilename(), 'Dokumentacja_Techniczna')==0) {
						echo '<h3>'.__('Dokumentacja techniczna', 'otwarte2013').'</h3>';
						$type = 'technical documentation';
					} else if(strcasecmp($fileinfo->getFilename(), 'Ewidencje_Parkowe')==0) {
						echo '<h3>'.__('Ewidencje parkowe', 'otwarte2013').'</h3>';
						$type = 'park documentation';
					}
			        else
			        	continue;
		        	$documents = new DirectoryIterator($root.DIRECTORY_SEPARATOR.$fileinfo->getFilename());

							?>
		        	<table class="folder-table">
						<tr>
							<th><input type="checkbox" class="check-all"></th>
							<th>Sygnatura</th>
							<th>JPEG</th>
							<th>PDF</th>
							<th>DjVu</th>
							<th>TEXT</th>
							<th>ALTO</th>
							<th><strong>Status</strong></th>
						</tr>
		        	<?php
		        	foreach($documents as $document) {
		        		if(!$document->isDir() && !$document->isDot()) {
		        			if(file_exists($root.DIRECTORY_SEPARATOR.$fileinfo->getFilename().DIRECTORY_SEPARATOR.$document->getFilename().DIRECTORY_SEPARATOR.$imported_file))
		        				continue;
		        			$documents_count++;
									//	var_dump($document->isDot());
									//	$delsymbols = array('.', '-');
		        			?>

							<tr class="tr-<?php echo str_replace('.', '-', $document->getFilename());?>">
								<td>
									<input type="checkbox" class="document-data" data-signature="<?php echo $document->getFilename();?>" data-type="<?php echo $type; ?>" data-path="<?php echo $root.DIRECTORY_SEPARATOR.$fileinfo->getFilename().DIRECTORY_SEPARATOR.$document->getFilename(); ?>">
								</td>
								<td>
									<strong><?php echo $document->getFilename(); ?></strong>
								</td>

								<?php

								foreach($document_folders as $folder) {
									$items = 0;
									if(is_dir($root.DIRECTORY_SEPARATOR.$fileinfo->getFilename().DIRECTORY_SEPARATOR.$document->getFilename().DIRECTORY_SEPARATOR.$folder)) {
										$subfolders = new DirectoryIterator($root.DIRECTORY_SEPARATOR.$fileinfo->getFilename().DIRECTORY_SEPARATOR.$document->getFilename().DIRECTORY_SEPARATOR.$folder);
										foreach($subfolders as $subfolder) {
											if(!$subfolder->isDir())
												$items++;
										}
									}

									?>
									<td><?php echo $items; ?></td>
									<?php
								}
								?>
								<td class="status"><span class="spinner" style="display: none;"></span></td>
							</tr>
		        			<?php
		        		}
		        	}
		        	?>
					</table>
		        	<?php
		        }
			}
			?>
		</div>

		<?php

		if($documents_count>0) {
			?>
			<div id="message" class="updated">
				<p><?php _e('Wybierz z poniższej listy dokumenty, które zamierzasz zaimportować do systemu.', 'otwarte2013'); ?></p>
			</div>
			<button id="documents-start" class="button-primary"><?php _e('Rozpocznij dodawanie dokumentów', 'otwarte2013'); ?></button>
			<div><br/><br/><strong>Parametry systemu:</strong><br/>Limit czasu wykonania: <?php echo ini_get('max_execution_time'); ?>s<br/>Limit pamięci: <?php echo WP_MAX_MEMORY_LIMIT; ?></div>
			<div id="documents-info">
				<span class="step">!</span><br/> <?php _e('Nie zamykaj tej strony, zanim otrzymasz komunikat o zakończeniu importu!', 'otwarte2013'); ?>
				<p id="info-import"><strong>Trwa import dokumentów</strong><br/>
					Aktualny dokument: <span id="info-current-document"></span><br/>
					Postęp: <span id="info-current-number">0</span> z <span id="info-all-number"></span><br/>
				</p>
			</div>
			<?php
		}
		else {
			echo '<div id="message" class="error"><p><strong>Katalog importu dokumentów nie zawiera plików.</strong></p><p>'.__('Aby skorzystać ze narzędzia importu, wyślij przez FTP lub SFTP pliki dokumentów do folderu o nazwie <strong>documents-upload</strong> w katalogu głównym WordPressa (').ABSPATH.'documents-upload).</p>
					<p><strong>Wymagana stuktura plików:</strong></p>
					<p>/documents-upload<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/Dokumementacja_techniczna<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/SYGNATURA.DOKUMENTU<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/JPEG<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/PDF<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ALTO<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/TEXT<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/DjVu<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/Obiekt-WUOZ-XXXXX.xml<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/Foto<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/karty_biale<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/karty_zielone<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ksiazki<br/></p></div>';
		}
	}
}