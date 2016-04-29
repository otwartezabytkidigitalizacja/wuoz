<?php 

// require css stylesheet and javasctipt files
require_once( get_template_directory() . '/functions/front_end_init.php' );

// ajax
require_once( get_template_directory() . '/functions/ajax.php' );

// require post types php
require_once( get_template_directory() . '/post-types/monument-post-type.php' );
require_once( get_template_directory() . '/post-types/document-post-type.php' );

// require options php
require_once( get_template_directory() . '/options/add-documents.php');
require_once( get_template_directory() . '/options/connect-documents.php');
require_once( get_template_directory() . '/options/theme-options.php');
require_once( get_template_directory() . '/options/create-monuments.php');

//require social login functions
require_once( get_template_directory() . '/functions/social_login.php' );

// register top right menu
register_nav_menu('top-right', 'Top Right Nav Menu');

//hide admin bar
if (!current_user_can( 'manage_options' )) show_admin_bar(false);

//Add Supports
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size( 270, 200, true ); // default Post Thumbnail dimensions   
}

if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'default-thumb', 270, 200, true ); //
	add_image_size( 'thumbnail', 400, 300, true); //mobile thumbs
    add_image_size( 'cover-size', 170, 126, true); //cover size
    add_image_size( 'single-thumbnail', 400, 270, true); //single thumbnail
    add_image_size( 'document-page-thumb', 120, 80, true); //single thumbnail
}



// user login
require_once( get_template_directory() . '/functions/user_login.php' );

// user favourites 

require_once( get_template_directory() . '/functions/user_logged.php' );

function oz_initial_settings($theme) {

    if(get_option('oz_2013_installed')!='d') {
        update_option('oz_2013_installed', 'd');


        $my_catalogues = array(
            'post_type' => 'page',
            'post_title' => __('Moje katalogi', 'otwarte2013'),
            'post_status' => 'publish');
        $search = array(
            'post_type' => 'page',
            'post_title' => __('Wyniki wyszukiwania', 'otwarte2013'),
            'post_status' => 'publish');
        $map = array(
            'post_type' => 'page',
            'post_title' => __('Mapa', 'otwarte2013'),
            'post_status' => 'publish');

        $wp_upload_dir = wp_upload_dir();
        $theme_dir = get_template_directory();

        $new_icon = $wp_upload_dir['path'].'/default_icon.png';
        $old_icon = $theme_dir.'/img/default_1270x200.png';
        copy($old_icon, $new_icon);

        $icon_filetype = wp_check_filetype(basename($new_icon));

        $icon_attachment = array (
            'guid' => $wp_upload_dir['url'] . '/' .basename($new_icon),
            'post_mime_type' => $icon_filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($new_icon)),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $icon = wp_insert_attachment($icon_attachment, $new_icon);

        update_option('oz_folder_icons', '['.$icon.']');

        $my_catalogues_id = wp_insert_post($my_catalogues, true);
        $search_id = wp_insert_post($search, true);
        $map_id = wp_insert_post($map, true);

        update_option('oz_my_catalogues_page', $my_catalogues_id);
        update_option('oz_search_page', $search_id);
        update_option('oz_map_page', $map_id);

        update_post_meta($my_catalogues_id, '_wp_page_template', 'my_folders.php');
        update_post_meta($search_id, '_wp_page_template', 'searchpage.php');
        update_post_meta($map_id, '_wp_page_template', 'mappage.php');
    }
}
add_action( 'init', 'oz_initial_settings' );

function oz_admin_error(){
    if(get_option('oz_maps_api')=='' && $_GET['page']!='theme-options') {
        echo '<div id="message" class="error"><p><strong>'.__('Nie wprowadziłeś klucza Google Maps API. Mapy nie bedą działać prawidłowo.', 'otwarte2013').'</strong><br/>'.__('Przejdź do strony', 'otwarte2013').' <strong><a href="/wp-admin/admin.php?page=theme-options">'.__('Opcje serwisu', 'otwarte2013').'</a></strong> '.__('aby wprowadzić klucz.', 'otwarte2013').'</p></div>';
    }
}
add_action('admin_notices', 'oz_admin_error');

// Get Google Maps API key

function get_maps_api_key() {
	return get_option('oz_maps_api');
}

// Google Maps coords to human coords

function get_human_lat($dec_lat) {
    if($dec_lat<0) {
        $dir = 'S';
        $dec_lat = $dec_lat * -1;
    }
    else {
        $dir = 'N';
    }

    $int_degree = floor($dec_lat);
    $int_min = floor(($dec_lat-$int_degree)*60);
    $int_sec = round(($dec_lat-$int_degree-($int_min/60))*3600, 2);

    return $int_degree.'&deg; '.$int_min.'\' '.$int_sec.'&quot; '.$dir;
}

function get_human_lng($dec_lng) {
    if($dec_lng<0) {
        $dir = 'W';
        $dec_lng = $dec_lng * -1;
    }
    else {
        $dir = 'E';
    }

    $int_degree = floor($dec_lng);
    $int_min = floor(($dec_lng-$int_degree)*60);
    $int_sec = round(($dec_lng-$int_degree-($int_min/60))*3600, 2);

    return $int_degree.'&deg; '.$int_min.'\' '.$int_sec.'&quot; '.$dir;
}

// Get regions (wojewodztwo) array 

function get_regions_array() {
    return array('dolnośląskie', 'kujawsko-pomorskie', 'lubelskie', 'lubuskie', 'łódzkie', 'małopolskie', 'mazowieckie', 'opolskie', 'podkarpackie', 'podlaskie', 'pomorskie', 'śląskie', 'świętokrzyskie', 'warmińsko-mazurskie', 'wielkopolskie', 'zachodniopomorskie'); 
}

// change and enchance search rules

require_once( get_template_directory() . '/functions/search_functions.php' );

// redirect normal users from wp-admin

add_action( 'admin_init', 'block_users_from_wp_admin' );

function block_users_from_wp_admin() {
    
    if ( ! current_user_can( 'manage_options' ) && '/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'] ) {
        wp_redirect(get_bloginfo('url'));
        exit;
    }
}

//connect document&monument 
function connect_mon_doc($mon, $doc) {
    $mon = intval($mon);
    $doc = intval($doc);
    $monument_meta = get_post_meta($mon, 'oz_documents', true);
    $document_meta = get_post_meta($doc, 'oz_monuments', true );

    if($monument_meta!='' && $monument_meta!='null')
        $monument_meta = json_decode($monument_meta);
    else
        $monument_meta=array();

    if($document_meta!='' && $document_meta!='null')
        $document_meta = json_decode($document_meta);
    else
        $document_meta=array();

    if($document_meta==null)
        $document_meta=array();
    if($monument_meta==null)
        $monument_meta=array();

    if(!is_array($document_meta))
        $document_meta=array();
    if(!is_array($monument_meta))
        $document_meta=array();

    if(!in_array($doc, $monument_meta))
        array_push($monument_meta, $doc);

    if(!in_array($mon, $document_meta))
        array_push($document_meta, $mon);

    update_post_meta($mon, 'oz_documents', json_encode($monument_meta));
    update_post_meta($doc, 'oz_monuments', json_encode($document_meta));
}

function disconnect_mon_doc($mon, $doc) {
    $mon = intval($mon);
    $doc = intval($doc);
    $monument_meta = json_decode(get_post_meta($mon, 'oz_documents', true));
    $document_meta = json_decode(get_post_meta($doc, 'oz_monuments', true ));

    if($monument_meta==null)
        $monument_meta=array();
    if($document_meta==null)
        $document_meta=array();

    $i=0;
    foreach ($monument_meta as $document) {
        if($document!=$doc) {
            $new_monument_meta[$i]=$document;
            $i++;
        }
    }

    $j=0;
    foreach ($document_meta as $monument) {
        if($monument!=$mon) {
            $new_document_meta[$i]=$monument;
            $j++;
        }
    }

    if(sizeof($new_document_meta)==0)
        $new_document_meta='';

    if(sizeof($new_monument_meta)==0)
        $new_monument_meta='';

    update_post_meta($mon, 'oz_documents', json_encode($new_monument_meta));
    update_post_meta($doc, 'oz_monuments', json_encode($new_document_meta));
}

function before_trash_monument($post_id) {
    $monument_meta = json_decode(get_post_meta($post_id, 'oz_documents', true ));
    foreach($monument_meta as $document) {
        disconnect_mon_doc($post_id, $document);
    }
}
add_action('trash_monument', 'before_trash_monument');


function before_trash_document($post_id) {
    $document_meta = json_decode(get_post_meta($post_id, 'oz_monuments', true ));
    foreach($document_meta as $monument) {
        disconnect_mon_doc($monument, $post_id);
    }
}
add_action('trash_document', 'before_trash_document');

function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}

function my_login_logo()  {
    wp_enqueue_style( "oz-admin-css", get_stylesheet_directory_uri() . '/stylesheets/admin.css');
}

add_action( 'login_enqueue_scripts', 'my_login_logo' );

add_filter( 'login_headerurl', 'custom_loginlogo_url' );
function custom_loginlogo_url($url) {
    return 'http://wuoz.otwartezabytki.pl';
}

?>
