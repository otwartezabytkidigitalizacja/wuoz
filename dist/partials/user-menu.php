<?php // loged in user menu
global $error, $current_user;

?>

<div class="login-cnt <?php echo

 is_user_logged_in() ? 'logged': ''; ?> large-4 columns">
  <?php if ( is_user_logged_in() ) : ?>
  <p class="ta-right"><?php echo

__('witaj, ', 'otwarte2013') . '<strong>' . $current_user->user_login . '</strong>' ?>
    <span> | </span>
    <a href="<?php echo

wp_logout_url(); ?>"> <?php _e('wyloguj', 'otwarte2013')?> </a>
  </p>
  <a data-reveal-id="settings-reveal" class="oz-button right"> <?php _e('Ustawienia', 'otwarte2013');?><span class="ico set-ico right"> </span> </a>
  <a href="<?php echo

get_permalink(get_option('oz_my_catalogues_page'))?>" class="oz-button right first-btn"> <?php _e('Moje katalogi', 'otwarte2013');?><span class="ico mycats-ico right"> </span>  </a>

  <?php get_template_part('partials/user', 'settings'); ?>
  <?php get_template_part('partials/user', 'favs'); ?>

<?php else : ?>
<?php $redirect = get_bloginfo('url') ?>

<a data-reveal-id="login-popup" class="oz-button right login-bttn"> <?php _e('Zaloguj', 'otwarte2013');?><span class="ico login-ico right"> </span> </a>
<div id="login-popup" class="reveal-modal small">
  <?php if (!empty($error) && $_POST['action'] == 'log-in') : ?>
  <?$msgs = $error->get_error_messages();
  foreach ($msgs as $msg ) {
   echo '<p class="srv-msg">' . $msg . '</p>';
 } ?>
 <script>
 jQuery(document).ready(function(){
   jQuery('#login-popup').foundation('reveal', 'open');
 });
 </script>
<?php elseif (isset($_GET['login']) && $_GET['login'] == 'check_email' ) : ?>
<script>
jQuery(document).ready(function(){
 jQuery('#login-popup').foundation('reveal', 'open');
})
</script>
<p class="srv-msg success"> <?php _e('Hasło zostało wysłane na podany email', 'otwarte2013 '); ?> </p>
<?php elseif (isset($_GET['login']) && $_GET['login'] == 'my_folders' ) : ?>
<?php $redirect = get_permalink(get_option('oz_my_catalogues_page')); ?>
<script>
jQuery(document).ready(function(){
 jQuery('#login-popup').foundation('reveal', 'open');
})
</script>
<p class="srv-msg success"> <?php _e('Zaloguj się, aby mieć dostęp do twoich katalogów', 'otwarte2013 '); ?> </p>
<?php elseif (isset($_GET['login']) && $_GET['login'] == 'pass_change' ) : ?>
<?php $redirect = get_permalink(get_option('oz_my_catalogues_page')); ?>
<script>
jQuery(document).ready(function(){
 jQuery('#login-popup').foundation('reveal', 'open');
})
</script>
<p class="srv-msg success"> <?php _e('Twoje hasło zostało zmienione! Zaloguj się ponownie', 'otwarte2013 '); ?> </p>
<?php endif ?>

<form name="loginform" id="loginform" method="post">
  <p class="login-username">
    <input type="text" required placeholder="<?php _e('email lub nazwa użytkownika', 'otwarte2013')?>" name="log" id="user_login" class="input" size="20" value="<?php echo

isset($_POST['log']) ? $_POST['log'] : '' ?>">
    <small class="error"><?php _e('Podaj nazwę użytkownika lub email', 'otwarte2013'); ?></small>
  </p>
  <p class="login-password">
    <input type="password"  placeholder="<?php _e('hasło', 'otwarte2013')?>" name="pwd" id="user_pass" class="input">
    <small class="error"><?php _e('Podaj hasło', 'otwarte2013'); ?></small>
  </p>
  <p class="login-submit">
    <a href="/#login" class="wp-submit link-form-submit large-12 columns large-centered">
      <span><?php _e('Zaloguj się', 'otwarte2013')?> </span>
      <span class="ico go-ico right"> </span>
    </a>
    <input type="submit" name="wp-submit">
    <input type="hidden" name="redirect_to" value="<?php echo

$redirect?>"/>
    <input type="hidden" name="action" value="log-in" />
    <input name="remember-me" type="hidden" id="rememberme" value="forever"/>
  </p>
  <?php do_social_login_buttons(); ?>
  <p style="text-align: center;">

    <a href="#register" data-reveal-id="register-popup">
      <?php _e('zarejestruj się', 'otwarte2013'); ?>
    </a>
    |
    <a href="http://wuoz.otwartezabytki.pl/wp-login.php?action=lostpassword"><?php _e('zapomniałem hasła', 'otwarte2013'); ?></a>
  </p>
</form>
<a class="close-reveal-modal ico"></a>
</div>

<!-- register popup -->
<div id="register-popup" class="reveal-modal small">
  <?php if (isset($_POST['action']) && $_POST['action'] == 'register') {?>
  <?
  if (!is_int($error)) {
    $msgs = $error->get_error_messages();
    foreach ($msgs as $msg ) {
     echo '<p class="srv-msg">' . $msg . '</p>';
   } ?>
   <script>
   jQuery(document).ready(function(){
    jQuery('#register-popup').foundation('reveal', 'open');
  })
   </script>
   <?php }
 } ?>
 <form name="registerform" id="registerform" method="post" >
  <p>
    <input required type="text" placeholder="<?php _e('nazwa użytkownika', 'otwarte2013')?>" name="user_login" id="user_login" class="input" value="" size="20" />
  </p>
  <p>
    <input required type="email" placeholder="<?php _e('email', 'otwarte2013')?>" name="user_email" id="user_email" class="input" value="" size="25" />
  </p>
  <p id="reg_passmail"><?php _e('Hasło zostanie wysłane e-mailem.', 'otwarte2013')?></p>
  <br class="clear">
  <a href="/#register" class="wp-submit link-form-submit large-12 columns large-centered">
    <span><?php _e('Zarejestruj Się', 'otwarte2013')?> </span>
    <span class="ico go-ico right"> </span>
  </a>
  <input type="hidden" name="redirect_to" value="<?bloginfo('url')?>" />
  <input type="hidden" name="action" value="register" />
  <input type="submit" name="wp-submit" />
  <a class="close-reveal-modal ico"></a>
</form>
</div>
<?php endif ?>
</div>
