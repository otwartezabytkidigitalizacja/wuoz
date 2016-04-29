<?php //php functions for logged in users

add_action( 'show_user_profile', 'show_user_favs_folders' );
add_action( 'edit_user_profile', 'show_user_favs_folders' );

function show_user_favs_folders( $user ) { ?>

<h3><?php _e('Dodatkowe Informacje o Użytkowniku', 'otwarte2013')?></h3>

<table class="form-table">

	<tr>
		<th><label for="users_favs"><?php _e('ID zaznaczeń', 'otwarte2013')?></label></th>

		<td>
			<input type="text" name="user_fold" value="<?php echo esc_attr( get_the_author_meta( 'user_favs', $user->ID ) ); ?>" class="regular-text" />
		</td>
	</tr>
	<tr>
		<th><label for="user_folders"><?php _e('JSON Folderów', 'otwarte2013')?></label></th>

		<td>
			<textarea name="user_folders" style="min-height: 300px; width: 100%"><?php echo esc_attr( get_the_author_meta( 'user_folders', $user->ID ) ); ?></textarea>
		</td>
	</tr>
	<tr>
		<th> <label for="user_postshow"> <?php _e('Ilość obiektów na stronie', 'otwarte2013')?> </label> </th>

		<td>
		<?php
			$selected =  esc_attr( get_the_author_meta( 'user_postshow', $user->ID ) );
			$options = array(8,16,20,24,32);
			empty($selected) ? $selected = 20 : '' ;
		?>
            <select name="user_postshow">
              <?php
              foreach($options as $option) :
              	echo $selected == $option ? '<option value="' . $option . '" selected>' . $option . ' </option>' : '<option value="' . $option . '">' . $option . '</option>' ;
          	  endforeach ?>

            </select>
		</td>
	</tr>

</table>
<?php }

add_action( 'personal_options_update', 'save_user_favs_folders' );
add_action( 'edit_user_profile_update', 'save_user_favs_folders' );

function save_user_favs_folders( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	update_user_meta( $user_id, 'user_favs', $_POST['user_favs'] );
	update_user_meta( $user_id, 'user_folders', $_POST['user_folders'] );
	update_user_meta( $user_id, 'user_postshow', $_POST['user_postshow'] );
}

// my favs echo and check

function echo_user_favs(){
	global $current_user;
 	$fav_string = get_the_author_meta( 'user_favs', $current_user->ID);
 	$fav_ids =  explode(',', $fav_string );
	 $args = array(
	  "post_type" => array('document', 'monument'),
	  'posts_per_page' => -1,
	  'post__in' => $fav_ids,
	  "orderby" => 'name',
	  'order' => 'ASC'
	  );

	 $favs = get_posts($args);
	if (!empty($favs)) {
	echo '<div class="scrollable">';
		echo '<p class="srv-msg large">';
		_e('Przeciągnij i upuść zaznaczenia (linki poniżej) na ikonę folderu lub na obszar otwartego folderu aby zorganizować twoje zaznaczenia', 'otwarte2013');
		echo "</p>";
		echo '<ul>';
			foreach($favs as $fav) {
				echo '<li data-post-id="' . $fav->ID . '"">';
					echo '<span class="ico remove-fav "> </span>';
					echo '<span class="ico ' . $fav->post_type . '"> </span>';
					echo '<a href="' . get_permalink($fav->ID) . '">';
						echo '<span>' . $fav->post_title . '</span>';
					echo '</a>';
				echo '</li>';
			}
		echo '</ul>';
	echo '</div>';
	} else {
		echo '<p class="srv-msg">';
			$favico = '<span class="ico fav-star-ico"> </span>';
			printf(__('Nie posiadasz zaznaczeń! Kliknij na %1$s i dodaj coś!', 'otwarte2013'), $favico);
		echo '</p>';
		}
	echo '<div class="button-group">';
  	if (!empty($favs)) {
	  	echo '<a href="#delete-all" class="ico del-favs left">';
	    echo '<span>' . __('Usuń Wszystkie', 'otwarte2013' ) . '</span>';
	  	echo '</a>';
	}
	echo '<a href="'. get_permalink(get_option('oz_my_catalogues_page')) . '" class="ico my-cats left">';
  	echo '<span>' .  __('Moje Katalogi', 'otwarte2013' ) . '</span>';
	echo '</a>';
	echo '</div>';
}
function is_fav($post_id) {
	global $current_user;
	$fav_ids =  explode(',', get_the_author_meta( 'user_favs', $current_user->ID ));
	return in_array($post_id, $fav_ids);
}

//add, remove and refresh functions are handled as actions in functions/ajax

function open_favs() {
		setcookie('open_favs', 1, time()+1209600, COOKIEPATH, COOKIE_DOMAIN, false);
}

function close_favs() {
	if (isset($_COOKIE['open_favs'])) {
		setcookie('open_favs', 0, time()+1209600, COOKIEPATH, COOKIE_DOMAIN, false);
	}
}

// user catalogues
function echo_user_folders() {
	global $current_user, $fol, $id, $folders;
    $folders = json_decode( get_the_author_meta( 'user_folders', $current_user->ID) );

    display_folders();

}
function display_folders() {
	global $folders, $id, $fol;
      if (!empty($folders)) :
	      foreach($folders as $id => $fol) :
	      get_template_part('partials/user', 'folder');
	      endforeach;
      endif;
      get_template_part('partials/user', 'addfolder');
}
// My settings save functions
function update_settings($user_id) {
	global $error;

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	if (isset($_POST['user_postshow'])) update_user_meta( $user_id, 'user_postshow', $_POST['user_postshow'] );
	if (isset($_POST['user_pass']) && isset($_POST['user_repass'])) {
			if ( strlen( $_POST['user_pass']  >= 8 )) {
				echo '<pre>';
				echo 'ja pierdole ? ';
				var_dump($_POST);
				echo '</pre>';
				wp_set_password( $_POST['user_pass'], $user_id );
				wp_redirect('/?login=pass_change');
			}
	}

}