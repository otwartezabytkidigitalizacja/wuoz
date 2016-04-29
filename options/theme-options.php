<?php
function register_theme_options() {
	add_menu_page(__('Opcje serwisu', 'otwarte2013'), __('Opcje serwisu', 'otwarte2013'), 'manage_options', 'theme-options', 'oz_theme_options', get_template_directory_uri().'/img/mon-ico-admin.png', 61);
}
add_action('admin_menu', 'register_theme_options');

function oz_theme_options_enqueue() {
	if (isset($_GET['page'])) :
		if($_GET['page']=='theme-options') {
			wp_enqueue_media();
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-accordion');
			wp_enqueue_style( "oz-admin-css", get_stylesheet_directory_uri() . '/stylesheets/admin.css');
			wp_enqueue_script('oz-theme-options-admin', get_stylesheet_directory_uri() . '/options/js/theme-options.js');
		}
	endif;
}
add_action( 'admin_enqueue_scripts', 'oz_theme_options_enqueue', 11 );

function oz_theme_options() {
	$hidden_field_name = 'att_submit_hidden';
	$names = array ('oz_video_code',
					'oz_maps_api',
					'oz_facebook',
					'oz_twitter',
					'oz_vimeo',
					'oz_search_page',
					'oz_my_catalogues_page',
					'oz_download_page',
					'oz_map_page',
					'oz_slides',
					'oz_folder_icons',
					'oz_fb_sc',
					'oz_tw_sc',
					'oz_gp_sc',
					'oz_fb_id',
					'oz_tw_id',
					'oz_gp_id',
					);
	for($i=0; $i<sizeof($names); $i++) {
		$values[$names[$i]]=get_option($names[$i]);
	}

	$pages_a = array('post_type' => 'page', 'posts_per_page' => -1);
    $pages_q = new WP_Query( $pages_a );
    $pages_count=0;
    while($pages_q->have_posts()) {
    	$pages_q->the_post();
    	$pages[$pages_count]['title']=get_the_title();
    	$pages[$pages_count]['id']=$pages_q->post->ID;
    	$pages_count++;
    }

	if(isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
		for($i=0; $i<sizeof($names); $i++) {
			$values[$names[$i]]=$_POST[$names[$i]];
			update_option($names[$i], $values[$names[$i]]);
		}

		update_post_meta($values['oz_my_catalogues_page'], '_wp_page_template', 'my_folders.php');
        update_post_meta($values['oz_search_page'], '_wp_page_template', 'searchpage.php');
        update_post_meta($values['oz_map_page'], '_wp_page_template', 'mappage.php');



		?>
		<div id="message" class="updated fade">
			<p><strong><?php _e('Opcje zostały zaktualizowane', 'otwarte2013'); ?></strong></p>
		</div>
		<?php
	}
	
	?>
	<form name="theme-options" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br/></div>
			<h2><?php _e('Opcje serwisu', 'otwarte2013'); ?></h2>
			<div id="theme-options-accordion">
				<h3><?php _e('Slider', 'otwarte2013'); ?></h3>
				<div>
					<table class="form-table">
						<tr>
							<th><strong><?php _e('Slajdy', 'otwarte2013'); ?></strong></th>
							<td>
								<div id="slides">
									<?php
									$slides = json_decode(stripcslashes($values['oz_slides']));
									foreach($slides as $slide) {
										?>
										<div class="slide">
											<div class="slide-text"><?php echo $slide->text;?></div>
											<div class="slide-list">
												<ul>
													<li class="li-1"><?php echo $slide->li_1; ?></li>
													<li class="li-2"><?php echo $slide->li_2; ?></li>
													<li class="li-3"><?php echo $slide->li_3; ?></li>
												</ul>
											</div>
											<div class="delete"></div>
										</div>
										<?php
									}
									?>
								</div>
								<p style="text-align: right"> <?php _e('Przeciągnij elementy, aby zmienić ich kolejność.', 'otwarte2013'); ?></p>
							</td>
						</tr>
						<tr>
							<th colspan="2" class="table-header">
								<strong><?php _e('Dodaj slajd', 'otwarte2013');?></strong>
							</th>
						</tr>
						<tr>
							<th><?php _e('Tekst slajdu', 'otwarte2013');?></th>
							<td><input type="text" id="slide-text" size="80"></td>
						</tr>
						<tr>
							<th><?php _e('Treść wypunktowań', 'otwarte2013');?></th>
							<td>
								<input type="text" id="slide-li-1" size="80"><br/>
								<input type="text" id="slide-li-2" size="80"><br/>
								<input type="text" id="slide-li-3" size="80"> 
								<button class="button" id="add-slide" type="button">Dodaj slajd</button>
							</td>
						</tr>
					</table>
					<input type="hidden" id="oz_slides" name="oz_slides" value='<?php echo stripslashes($values['oz_slides']); ?>'>
				</div>
				<h3><?php _e('Okładki katalogów', 'otwarte2013'); ?></h3>
				<div>	
					<table class="form-table">
						<tr>
							<th><strong><?php _e('Okładki', 'otwarte2013'); ?></strong></th>
							<td>
								<div id="folder-icons">
									<?php
									$icons = json_decode(stripcslashes($values['oz_folder_icons']));
									foreach ($icons as $icon) {
										$image = wp_get_attachment_image_src($icon);
										$image = $image[0];
										?>
										<div class="folder-icon" data-icon-id="<?php echo $icon; ?>" style="background-image: url(<?php echo $image; ?>);"><div class="delete"></div></div>
										<?php
									}
									?>
									<div id="add-folder-icon"></div>
								</div>
							</td>
						</tr>
					</table>
					<input type="hidden" name="oz_folder_icons" id="oz_folder_icons" value="<?php echo $values['oz_folder_icons']; ?>">
				</div>
				<h3><?php _e('Sieci społecznościowe', 'otwarte2013'); ?></h3>
				<div>
					<p><?php _e('Wprowadź linki do profili w sieciach społecznościowych. Jeżeli pozostawisz pole puste, przycisk danej sieci nie bedzie wyświetlany.', 'otwarte2013'); ?></p>
					<table class="form-table">
					<tr>
						<th><strong>Facebook</strong></th>
						<td><input type="text" name="oz_facebook" size="80" value="<?php echo $values['oz_facebook']; ?>"></td>
					</tr>
					<tr>
						<th><strong>Twitter</strong></th>
						<td><input type="text" name="oz_twitter" size="80" value="<?php echo $values['oz_twitter']; ?>"></td>
					</tr>
					<tr>
						<th><strong>Vimeo</strong></th>
						<td><input type="text" name="oz_vimeo" size="80" value="<?php echo $values['oz_vimeo']; ?>"></td>
					</tr>
					</table>
				</div>
				<h3><?php _e('Ustawienia logowania', 'otwarte2013'); ?></h3>
				<div>
					<table class="form-table">
						<tr>
							<th><strong>Facebook</strong></th>
							<td></td>
						</tr>
						<tr>
							<th>Application ID</th>
							<td>
								<input type="text" name="oz_fb_id" value="<?php echo $values['oz_fb_id']; ?>">
							</td>
						</tr>
						<tr>
							<th>Application secret</th>
							<td>
								<input type="text" name="oz_fb_sc" value="<?php echo $values['oz_fb_sc']; ?>">
							</td>
						</tr>
						<tr>
							<th><strong>Twitter</strong></th>
							<td>
							</td>
						</tr>
						<tr>
							<th>Application key</th>
							<td>
								<input type="text" name="oz_tw_id" value="<?php echo $values['oz_tw_id']; ?>">
							</td>
						</tr>
						<tr>
							<th>Application secret</th>
							<td>
								<input type="text" name="oz_tw_sc" value="<?php echo $values['oz_tw_sc']; ?>">
							</td>
						</tr>
						<tr>
							<th><strong>Google +</strong></th>
							<td>
							</td>
						</tr>
						<tr>
							<th>Application ID</th>
							<td>
								<input type="text" name="oz_gp_id" value="<?php echo $values['oz_gp_id']; ?>">
							</td>
						</tr>
						<tr>
							<th>Application secret</th>
							<td>
								<input type="text" name="oz_gp_sc" value="<?php echo $values['oz_gp_sc']; ?>">
							</td>
						</tr>
					</table>
				</div>
				<h3><?php _e('Ustawienia zaawansowane', 'otwarte2013'); ?></h3>
				<div>
					<table class="form-table">
						<tr>
							<th><strong><?php _e('Kod wideo', 'otwarte2013'); ?></strong></th>
							<td>
								<textarea name="oz_video_code" cols="70" rows="7"><?php echo stripslashes($values['oz_video_code']); ?></textarea>
							</td>
						</tr>
						<tr>
							<th><strong>Google Maps API key</strong></th>
							<td><input type="text" name="oz_maps_api" size="70" value="<?php echo $values['oz_maps_api']; ?>"></td>
						</tr>
						<tr>
							<th><strong><?php _e('Strona wyników wyszukiwania', 'otwarte2013'); ?></strong></th>
							<td>
								<select name="oz_search_page">
								<?php
									for($i=0; $i<$pages_count; $i++) {
										if($pages[$i]['id']==$values['oz_search_page'])
											echo '<option value="'.$pages[$i]['id'].'" selected="selected">'.$pages[$i]['title'].'</option>';
										else
											echo '<option value="'.$pages[$i]['id'].'">'.$pages[$i]['title'].'</option>';
									}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<th><strong><?php _e('Strona "Moje katalogi"', 'otwarte2013'); ?></strong></th>
							<td>
								<select name="oz_my_catalogues_page">
								<?php
									for($i=0; $i<$pages_count; $i++) {
										if($pages[$i]['id']==$values['oz_my_catalogues_page'])
											echo '<option value="'.$pages[$i]['id'].'" selected="selected">'.$pages[$i]['title'].'</option>';
										else
											echo '<option value="'.$pages[$i]['id'].'">'.$pages[$i]['title'].'</option>';
									}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<th><strong><?php _e('Strona pobierania', 'otwarte2013'); ?></strong></th>
							<td>
								<select name="oz_download_page">
								<?php
									for($i=0; $i<$pages_count; $i++) {
										if($pages[$i]['id']==$values['oz_download_page'])
											echo '<option value="'.$pages[$i]['id'].'" selected="selected">'.$pages[$i]['title'].'</option>';
										else
											echo '<option value="'.$pages[$i]['id'].'">'.$pages[$i]['title'].'</option>';
									}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<th><strong><?php _e('Strona z mapą', 'otwarte2013'); ?></strong></th>
							<td>
								<select name="oz_map_page">
								<?php
									for($i=0; $i<$pages_count; $i++) {
										if($pages[$i]['id']==$values['oz_map_page'])
											echo '<option value="'.$pages[$i]['id'].'" selected="selected">'.$pages[$i]['title'].'</option>';
										else
											echo '<option value="'.$pages[$i]['id'].'">'.$pages[$i]['title'].'</option>';
									}
								?>
								</select>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Zapisz'); ?>"/></p>
		</div>
	</form>
	<?php
}