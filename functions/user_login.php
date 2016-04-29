<?php
function go_home(){
  wp_redirect( home_url() );
  exit();
}
add_action('wp_logout','go_home');

function email_or_username_login( $user, $username, $password ) {
	if ( is_a( $user, 'WP_User' ) )
		return $user;

	if ( !empty( $username ) ) {
		$username = str_replace( '&', '&amp;', stripslashes( $username ) );
		$user = get_user_by( 'email', $username );
		if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status )
			$username = $user->user_login;
	}

	return wp_authenticate_username_password( null, $username, $password );
}

remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
add_filter( 'authenticate', 'email_or_username_login', 20, 3 );

// in template register actions

//register email

add_filter( 'wp_mail_from', 'otwarte_zabytki_from_mail' );
add_filter( 'wp_mail_from_name', 'otwarte_zabytki_from_name' );

function otwarte_zabytki_from_mail($email) {
  return 'administrator@otwarte-zabytki.pl';
}

function otwarte_zabytki_from_name($name) {
  return 'Otwarte Zabytki';
}

// wp-login from homepage

function user_login_register() {
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) :
        global $error;
    $rem = 'false';
    isset($_POST['remember-me']) ? $rem = 'forever' :'' ;
    $login = wp_signon( array( 'user_login' => $_POST['log'], 'user_password' => $_POST['pwd'], 'remember' => $rem ), false );
    // echo '<pre>';
    // var_dump($_POST);
    // echo '</pre>';
    if ( empty($_POST['log']) || empty($_POST['pwd']) ) $login = new WP_error(666, __('Żadne pole nie może być puste', 'otwarte2013'));

    $user_name = $_POST['log'];
    if (!username_exists($user_name)){
      $login = new WP_error(666, __('Niewłaściwa nazwa użytkownika. <a href="wp-login.php?action=lostpassword">Nie pamiętasz hasła?</a>', 'otwarte2013'));
    }
    
    $user = get_user_by( 'login', $user_name);
    if ($user){
      $password = $_POST['pwd'];
      $hash = $user->data->user_pass;
      if (!wp_check_password($password, $hash)){
          $login = new WP_error(666, __('Wprowadzone hasło dla użytkownika <strong>'. $user_name . '</strong> nie jest poprawne. <a href="wp-login.php?action=lostpassword">Nie pamiętasz hasła?</a>', 'otwarte2013'));
      }
    }
    $error = $login;
    if (!is_wp_error($login)) wp_redirect($_POST['redirect_to']);

    endif;

    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'register' ) :
        global $error;
    $user_name = $_POST['user_login'];
    $user_email = $_POST['user_email'];
    $user_name = sanitize_user( $user_name );
    $user_id = username_exists($user_name);
    $user_id = username_exists($user_email);
    $random_password = '';
    if (is_email($user_email)) {

        if ( !$user_id and email_exists($user_email) == false ) {
            $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
            $user_id = wp_create_user( $user_name, $random_password, $user_email );
        } else {
            $random_password = __('User already exists.  Password inherited.');
            $user_id = wp_create_user( $user_name, $random_password, $user_email );
        }
    } else {
       $user_id = new WP_error(666, __('Nieprawidłowy lub pusty adres email', 'otwarte2013') );
   }

   // echo '<pre>';
   // var_dump($_POST);
   // var_dump($user_id);
   // echo '</pre>';
   $error = $user_id;
     if (!is_wp_error($user_id)) {
      $message = __("Witaj na portalu! \n Do logowania użyj adresu email, a twoje hasło to: ", 'otwarte2013') . $random_password;
      wp_mail( $user_email, __('[Rejestracja]Twoje Hasło do portalu Otwarte Zabytki', 'otwarte2013') , $message );
      wp_redirect('/?login=check_email');
    }
   endif;
}
add_action('wp_head', 'user_login_register');
