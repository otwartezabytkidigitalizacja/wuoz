 <?php
global $error, $current_user;
get_currentuserinfo();
if ( isset( $_POST['user_postshow'] ) || isset( $_POST['repass'] ) ) update_settings( $current_user->ID );

?>

 <div id="settings-reveal" class="reveal-modal small">
    <a class="close-reveal-modal ico"></a>
    <form id="user-settings" name="user-settings" method="post" data-abide>
      <div class="info-cnt">
        <div class="large-12 column large-centered ">
        <h3 class="row"> <?php _e( 'Informacje i Ustawienia', 'otwarte2013' ) ;?> </h3>
        <p class="row set-cnt">
          <span class="set-label large-5 column"><?php _e( 'Nazwa użytkownika', 'otwarte2013' )?> </span>
          <span class="set-value large-7 column"> <?php echo $current_user->user_login ?> </span>
        </p>
        </div>

        <div class="large-12 column large-centered ">
        <p class="row set-cnt">
          <span class="set-label large-5 column"> <?php _e( 'Twój email', 'otwarte2013' )?> </span>
          <span class="set-value large-7 column"> <?php echo $current_user->user_email ?> </span>
        </p>
        </div>

            <div class="large-12 column large-centered">
            <p class="row set-cnt">
              <span class="set-label large-5 column"> <?php _e( 'Liczba miniatur na stronie', 'otwarte2013' )?> </span>
              <span class="set-value large-7 column">
                <?php
    $selected =  esc_attr( get_the_author_meta( 'user_postshow', $current_user->ID ) );
    $options = array(2,8, 16, 20, 24, 32);
    empty( $selected ) ? $selected = 20 : '' ;
    ?>
                    <select name="user_postshow">
                      <?php
    foreach ( $options as $option ) :
      echo $selected == $option ? '<option value="' . $option . '" selected>' . $option . ' </option>' : '<option value="' . $option . '">' . $option . '</option>' ;
    endforeach ?>

                </select>
              </span>
            </p>
            </div>
          </div>
          <h5 class="ta-center"> <?php _e( 'Zmiana hasła', 'otwarte2013 ' )?> </h5>
          <p class="row large-10 column large-centered pass-cnt"><?php _e('Zostaw poniższe pola puste, jeżeli nie chcesz zmieniać hasła.') ?></p>

          <!-- <p class="row large-10 column large-centered pass-cnt">
            <input required placeholder="<?php _e( 'stare hasło', 'otwarte2013' ) ?>" name="repass" type="password" />
            <small class="error"> <?php _e( '' )?> </small>
          </p> -->
          <p class="row large-10 column large-centered pass-cnt">
            <input id="user_pass" placeholder="<?php _e( 'nowe hasło', 'otwarte2013' ) ?>" name="user_pass" type="password" />
            <small class="error"> <?php _e( 'Nowe hasło musi się składać z conajmniej 8 znaków zawierających 1 dużą literę, 1 małą literę i 1 cyfrę ' )?> </small>
          </p>
          <p class="row large-10 column large-centered pass-cnt">
            <input required placeholder="<?php _e( 'powtórz hasło', 'otwarte2013' ) ?>" name="user_re pass" type="password" pattern="newpass" data-equalto="user_pass" />
            <small class="error"> <?php _e( 'Hasłą muszą być identyczne', 'otwarte2013' )?> </small>
          </p>
          <a class="row wp-submit link-form-submit large-12 columns large-centered">
            <?php _e( 'Zapisz zmiany', 'otwarte2013' ) ?>
          </a>
      </form>
 </div>
