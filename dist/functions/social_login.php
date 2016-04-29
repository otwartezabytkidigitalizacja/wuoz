<?php
function social_login() {
	require_once( get_template_directory() . "/libraries/hybridauth/Hybrid/Auth.php" );
	if(isset($_REQUEST['social_login']) && isset($_REQUEST['provider'])) {

		if(!is_user_logged_in()){
			if($_REQUEST['social_login']='login') {
				$provider = $_REQUEST['provider'];
				$base_url = get_template_directory_uri() . "/libraries/hybridauth/";
				if(substr($base_url, 0, 4) != "http")
					$base_url = home_url() . $base_url;

				$config_array = array(
					"base_url" => $base_url,
					"providers" => array (
						"Twitter" => array (
							"enabled" => true,
							"keys" => array ( "key" => get_option('oz_tw_id'), "secret" => get_option('oz_tw_sc') )
						),
						"Facebook" => array (
							"enabled" => true,
							"keys" => array ( "id" => get_option('oz_fb_id'), "secret" => get_option('oz_fb_sc') )
						),
						"Google" => array (
							"enabled" => true,
							"keys" => array ( "id" => get_option('oz_gp_id'), "secret" => get_option('oz_gp_sc') )
						)
					)
				);
	 			$hybridauth = new Hybrid_Auth($config_array);
	 			$adapter = $hybridauth->authenticate($provider);
	 			$user_profile = $adapter->getUserProfile();

	 			$args = array(
	 				'meta_key' => 'social_identifier',
	 				'meta_value' => $provider.'_'.$user_profile->identifier);
	 			$users = get_users($args);
	 			// var_dump($users);
	 			if(sizeof($users)>0) {
	 				//echo "social";
	 				$user = $users[0];
		 			wp_set_current_user($user->ID);
		 			wp_set_auth_cookie($user->ID, ture);
	 			}
	 			else if ($user = get_user_by('email', $user_profile->email)) {
	 				//echo "email";
	 				wp_set_current_user($user->ID);
		 			wp_set_auth_cookie($user->ID, ture);
	 			}
	 			else {
	 				//echo "create";
	 			 	$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
	 			 	if($user_profile->displayName!='' && !username_exists(str_replace(' ', '_', $user_profile->displayName)))
	 			 		$username = str_replace(' ', '_', $user_profile->displayName);
	 				else {
	 					$i=1;
	 					$username=str_replace(' ', '_', $user_profile->displayName).'_'.$i;
	 					while(username_exists($username)) {
	 						$i++;
	 						$username=str_replace(' ', '_', $user_profile->displayName).'_'.$i;
	 					}
	 				}
	 				$user_email = $user_profile->email;

	 				// echo $user_email.' '.$username.' ';

	 				if($provider=="Twitter") {
	 					$user_email = $user_profile->displayName.'@twitter.com';
	 				}

					$user_id = wp_create_user( $username, $random_password, $user_email );

					if($user_id) {
						update_user_meta( $user_id, 'social_identifier', $provider.'_'.$user_profile->identifier);
						$userdata = array(
							'ID' => $user_id,
							'first_name' => $user_profile->firstName,
							'last_name' => $user_profile->lastName,
							'user_url' => $user_profile->profileURL);
						wp_update_user( $userdata );
						wp_set_current_user($user_id);
		 				wp_set_auth_cookie($user_id, ture);
					}
	 			}
			}
		}
	}
}

add_action('after_setup_theme', 'social_login');

function do_social_login_buttons() {

	if(get_option('oz_fb_id')!='' && get_option('oz_fb_sc')!='')
		$fb=true;
	if(get_option('oz_tw_id')!='' && get_option('oz_tw_sc')!='')
		$tw=true;
	if(get_option('oz_gp_id')!='' && get_option('oz_gp_sc')!='')
		$gp=true;
	if($fb || $tw || $gp) {
		echo '<p class="social-login-caption">'.__('Zaloguj się korzystając z konta', 'otwarte2013').'</p>';
		echo '<div id="wp-social-login-connect-options">';
		if($fb)
			echo '<a rel="nofollow" href="'.home_url().'?social_login=login&provider=Facebook" title="Facebook" class="ico wsl_connect_with_provider Facebook">f</a>';
		if($tw)
			echo '<a rel="nofollow" href="'.home_url().'?social_login=login&provider=Twitter" title="Twitter" class="ico wsl_connect_with_provider Twitter">t</a>';
		if($gp)
			echo '<a rel="nofollow" href="'.home_url().'?social_login=login&provider=Google" title="Google +" class="ico wsl_connect_with_provider Google">g+</a>';
		echo '</div>';
	}
}
?>