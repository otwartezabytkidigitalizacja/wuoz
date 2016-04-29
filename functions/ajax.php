<?php

if(is_admin()) {
	add_action('wp_ajax_map_objects_all', 'otwarte_map_objects_all');
	add_action('wp_ajax_nopriv_map_objects_all', 'otwarte_map_objects_all');
}

function otwarte_map_objects_all() {
	$count_posts = wp_count_posts('monument');
	$published_posts = $count_posts->publish;
	$published_posts = $published_posts/500;
	$published_posts = ceil($published_posts);
	echo $published_posts;
	die();
}

if(is_admin()) {
	add_action('wp_ajax_map_objects', 'otwarte_map_objects');
	add_action('wp_ajax_nopriv_map_objects', 'otwarte_map_objects');
}

function otwarte_map_objects() {

 $paged = $_POST['paged'];

	$args = array('post_type' => 'monument', 'posts_per_page' => 500, 'orderby'=>'title', 'paged'=>$paged);
	$qs = get_posts($args);

	$i=0;
			if ($qs){
				foreach($qs as $q) {setup_postdata($q);
					$lat = get_post_meta($q->ID, 'oz_lat', true );
					$lng = get_post_meta($q->ID, 'oz_lng', true );
					if($lat!='' && $lng!='') {
						$posts[$i]['lat']=get_post_meta($q->ID, 'oz_lat', true );
						$posts[$i]['lng']=get_post_meta($q->ID, 'oz_lng', true );
						$posts[$i]['id']=$q->ID;
						$i++;
					}
				}
			}
		wp_reset_postdata();

// google
// id 349576897701.apps.googleusercontent.com
//secret aqdu7wWDs336gpXp6y7Rij9E

	if(sizeof($posts)>0)
		echo json_encode($posts);
	else
		echo '0';
	die();
}

if(is_admin()) {
	add_action('wp_ajax_map_single_object', 'otwarte_map_single_object');
	add_action('wp_ajax_nopriv_map_single_object', 'otwarte_map_single_object');
}


function otwarte_map_single_object() {
	$object_id = $_POST['object_id'];
	$object['lat'] = get_post_meta($object_id, 'oz_lat', true );
	$object['lng'] = get_post_meta($object_id, 'oz_lng', true );

	echo json_encode($object);
	die();
}

if(is_admin()) {
    add_action('wp_ajax_monument_data_multiple', 'otwarte_monument_data_multiple');
    add_action('wp_ajax_nopriv_monument_data_multiple', 'otwarte_monument_data_multiple');
}

function otwarte_monument_data_multiple() {
    $ids = $_POST['ids'];
    $a = array('post_type' => 'monument', 'post__in' => $ids, 'posts_per_page' => -1);
    $q = new WP_Query($a);
    $i=0;
    while($q->have_posts()) {
        $q->the_post();
        $posts[$i]['name']=get_the_title();
        $posts[$i]['excerpt']=get_the_excerpt();
        $posts[$i]['permalink']=get_permalink();
        $i++;
    }
    echo json_encode($posts);
    die();
}

if(is_admin()) {
	add_action('wp_ajax_monument_data', 'otwarte_monument_data');
	add_action('wp_ajax_nopriv_monument_data', 'otwarte_monument_data');
}

function otwarte_monument_data() {
	$id = $_POST['id'];
	$a = array('post_type' => 'monument', 'p' => $id);
	$q = new WP_Query($a);
	while($q->have_posts()) {
		$q->the_post();
		$posts['name']=get_the_title();
		$posts['excerpt']=get_the_excerpt();
		$posts['permalink']=get_permalink();
	}
	echo json_encode($posts);
	die();
}

// user ajax actions

function add_to_fav() {
	// returns 1 for adding to favs and 0 if the ID was already in fav
	global $current_user;
	$post_id = $_POST['post_id'];
	$fav_ids =  explode(',', get_the_author_meta( 'user_favs', $current_user->ID ));
	if (in_array($post_id, $fav_ids)) {
		return 0;
	} else {
		array_push($fav_ids, $post_id);
		$update = implode(',', $fav_ids);
		update_user_meta( $current_user->ID, 'user_favs', $update );
		return 1;
	}
}

add_action('wp_ajax_add_to_fav', 'add_to_fav');

function remove_single_fav() {
	// returns 1 for removing to favs and 0 if there was nothing to remove
	global $current_user;
	$post_id = $_POST['post_id'];
	$fav_ids =  explode(',', get_the_author_meta( 'user_favs', $current_user->ID ));
	if (in_array($post_id, $fav_ids)) {
		$torem = array_search($post_id, $fav_ids);
		unset($fav_ids[$torem]);
		$update = implode(',', $fav_ids);
		update_user_meta( $current_user->ID, 'user_favs', $update) ;
	}
}

add_action('wp_ajax_remove_fav', 'remove_single_fav');

function remove_all_favs() {
	global $current_user;
	update_user_meta( $current_user->ID, 'user_favs', '') ;
}

add_action('wp_ajax_remove_all_favs', 'remove_all_favs');

// echo user favs is located in functions/user_logged
add_action('wp_ajax_refresh_all_favs', 'echo_user_favs_ajax');

function echo_user_favs_ajax(){
	echo_user_favs();
	// stops ajax 0 response
	die();
}

// keep the favs open
add_action( 'wp_ajax_open_favs', 'open_favs');
add_action( 'wp_ajax_close_favs', 'close_favs');

// get the heavy vimeo iframe

function load_vimeo() {
	$video_iframe = stripslashes(get_option('oz_video_code'));
	echo $video_iframe;
	die();
}

add_action( 'wp_ajax_nopriv_load_vimeo', 'load_vimeo');
add_action( 'wp_ajax_load_vimeo', 'load_vimeo');

function echo_search_results_ajax(){
	echo_search_results();
	// stops ajax 0 response
	die();
}

// keep the favs open
add_action( 'wp_ajax_get_page_objects', 'echo_search_results_ajax');
add_action( 'wp_ajax_nopriv_get_page_objects', 'echo_search_results_ajax');

//get user folders

function add_user_folder() {
	// returns 1 for adding to favs and 0 if the ID was already in fav
	global $current_user, $folders, $fol, $id;
	$fname = $_POST['name'];
	$fcover = $_POST['cover'];
	$folders =  json_decode(get_the_author_meta( 'user_folders', $current_user->ID ), true);
	empty($folders) ? $folders = array() : '';
	//var_dump($folders);
	$fadd = array(
		"name" => $fname,
		"cover" => $fcover,
		"ids" => ''
	);
	array_push($folders, $fadd);
	$update = addslashes(json_encode($folders));
	update_user_meta( $current_user->ID, 'user_folders', $update );

	// to from array to indexed object
	$folders = json_encode($folders);
	$folders = json_decode($folders);

    display_folders();

     die();

}
add_action( 'wp_ajax_add_user_folder', 'add_user_folder');

function remove_user_folder() {
	global $current_user, $folders, $fol, $id;
	$folders =  json_decode(get_the_author_meta( 'user_folders', $current_user->ID ), true);
	unset($folders[$_POST['fid']]);
	$update = addslashes(json_encode($folders));
	update_user_meta( $current_user->ID, 'user_folders', $update );

	// to from array to indexed object
	$folders = json_encode($folders);
	$folders = json_decode($folders);

	display_folders();

     die();

}
add_action( 'wp_ajax_remove_user_folder', 'remove_user_folder');

function save_after_change() {
	global $current_user;
	$folders =  json_decode(get_the_author_meta( 'user_folders', $current_user->ID ), true);
	//echo $_POST['folder'];
	//var_dump($folders);
	$folders[$_POST['folder']]['ids'] = $_POST['posts'];
	$update = addslashes(json_encode($folders));
	update_user_meta( $current_user->ID, 'user_folders', $update );

	die();
}
add_action( 'wp_ajax_save_after_change', 'save_after_change');

//load user folder

function load_user_folder() {
	echo_search_results();
	die();
}
add_action( 'wp_ajax_load_user_folder', 'load_user_folder');

function get_single_object() {
	global $object, $prev_id, $next_id, $inside, $mon_docs, $mon_id;
	if (isset($_POST['object_id'])) :
		$object = get_post($_POST['object_id']);

		if (isset($_POST['next'])) $next_id = $_POST['next'];
		if (isset($_POST['prev'])) $prev_id = $_POST['prev'];
		if (isset($_POST['inside'])) $inside = $_POST['inside'];
		if (isset($_POST['mon_docs'])) $mon_docs = $_POST['mon_docs'];
		if (isset($_POST['mon_id'])) $mon_id = $_POST['mon_id'];
		if ($object->post_status=='publish') {
			if($object->post_type == 'monument')
				get_template_part('/single/single', 'monument');
			else
				get_template_part('/single/single', 'document');
		} else {
			_e('Obiekt nie jest dostępny w danym momencie', 'otwarte2013');
		}
	die();
	else : die();
	endif;
}
add_action( 'wp_ajax_get_single_object', 'get_single_object');
add_action( 'wp_ajax_nopriv_get_single_object', 'get_single_object');

if(is_admin()) {
	add_action('wp_ajax_upload_document', 'otwarte_upload_document');
	add_action('wp_ajax_nopriv_upload_document', 'otwarte_upload_document');
}

function otwarte_upload_document() {
	$path = $_REQUEST['path'];
	$type = $_REQUEST['type'];
	$signature = $_REQUEST['signature'];
	$monument_id = $_REQUEST['monument'];

	if(file_exists($path.DIRECTORY_SEPARATOR.'.wuoz-locked')) {
		echo "locked";
		die();
	}

	$f = fopen($path.DIRECTORY_SEPARATOR.'.wuoz-locked', w);
	fclose($f);

	$xml = glob($path.DIRECTORY_SEPARATOR.'*.xml');
	$xml_contents = simplexml_load_file($xml[0]);
	$namespaces = $xml_contents->getNameSpaces(true);
	$title = $xml_contents->xpath('//dc:title');
	$title = $title[0];

	$jpegs = $xml_contents->xpath("//mets:fileGrp[@ID='RCGrp']/mets:file/mets:FLocat");
	if(sizeof($jpegs)==0) {
		$jpgi = 0;
		if ($handle = opendir($path.DIRECTORY_SEPARATOR.'JPEG')) {
		    while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != "..") {
		         $jpegs[$jpgi]='JPEG'.DIRECTORY_SEPARATOR.$entry;
		         $jpgi++;
		        }
		    }
		    closedir($handle);
		}
		sort($jpegs);
		$noxml = true;
	}
	$altos = $xml_contents->xpath("//mets:fileGrp[@ID='RTGrp']/mets:file/mets:FLocat");

	if(sizeof($altos)==0) {
		$altoi = 0;
		if ($handle = opendir($path.DIRECTORY_SEPARATOR.'ALTO')) {
		    while (false !== ($entry = readdir($handle))) {
		        if ($entry != "." && $entry != "..") {
		         $altos[$altoi]='ALTO'.DIRECTORY_SEPARATOR.$entry;
		         $altoi++;
		        }
		    }
		    closedir($handle);
		}
		sort($altos);
	}

	// print_r($jpegs);
	// print_r($altos);

	$wp_upload_dir = wp_upload_dir();
	$wp_upload_url = $wp_upload_dir['url'].DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.str_replace(' ', '_', $type).DIRECTORY_SEPARATOR.$signature;
	$wp_upload_dir = $wp_upload_dir['basedir'].DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.str_replace(' ', '_', $type).DIRECTORY_SEPARATOR.$signature;

	if (!file_exists($wp_upload_dir)) {
	    mkdir($wp_upload_dir, 0777, true);
	}
	//----- JPEG, TEXT, ALTO->db
	if (!file_exists($wp_upload_dir.DIRECTORY_SEPARATOR.'JPEG')) {
	    mkdir($wp_upload_dir.DIRECTORY_SEPARATOR.'JPEG', 0777, true);
	}

	$remove_from_path = array("JPEG/", ".jpg");

	$pages = sizeof($jpegs);
	$i=0;
	foreach($jpegs as $jpeg) {
		if($noxml) {
			$filename = str_replace($remove_from_path, '', $jpeg);
			$old_file = $path.DIRECTORY_SEPARATOR.$jpeg;
			$new_file = $wp_upload_dir.DIRECTORY_SEPARATOR.$jpeg;
		}
		else {
			$filename = str_replace($remove_from_path, '', $jpeg->attributes('xlink', true)->{'href'});
			$old_file = $path.DIRECTORY_SEPARATOR.$jpeg->attributes('xlink', true)->{'href'};
			$new_file = $wp_upload_dir.DIRECTORY_SEPARATOR.$jpeg->attributes('xlink', true)->{'href'};
		}

		$content_path = $path.DIRECTORY_SEPARATOR.'TEXT'.DIRECTORY_SEPARATOR.$filename.'.Txt';
		$alto_path = $path.DIRECTORY_SEPARATOR.'ALTO'.DIRECTORY_SEPARATOR.$filename.'.xml';

		if(file_exists($content_path))
			$content = file_get_contents($content_path);
		else
			$content = '';

		if(file_exists($alto_path))
			$alto = file_get_contents($alto_path);
		else
			$alto = '';

		copy($old_file, $new_file);
		if($pages>1)
			$page = ' (str. '.($i+1).')';
		else
			$page = '';

		$file_type = wp_check_filetype(basename($new_file), null);
		$attachment_data = array (
			'guid' => $wp_upload_url . '/JPEG/' . basename( $new_file ),
			'post_mime_type' => $file_type['type'],
			'post_title' => $title.' ['.$signature.']'.$page,
			'post_content' => $content,
			'post_status' => 'inherit'
		);
		$attachment = wp_insert_attachment($attachment_data, $new_file);
		echo "[AT]".$attachment."[/AT]\n";
		$attach_data = wp_generate_attachment_metadata( $attachment, $new_file );
		wp_update_attachment_metadata( $attachment,  $attach_data );
		update_post_meta($attachment, 'oz_alto', $alto);
		$jpeg_ids[$i]=$attachment;
		$i++;
	}

	echo '<br>jpegs added</br>';

	//----- ALTO files
	if (!file_exists($wp_upload_dir.DIRECTORY_SEPARATOR.'ALTO')) {
	    mkdir($wp_upload_dir.DIRECTORY_SEPARATOR.'ALTO', 0777, true);
	}

	foreach($altos as $alto) {
		$old_file = $path.DIRECTORY_SEPARATOR.$alto->attributes('xlink', true)->{'href'};
		$new_file = $wp_upload_dir.DIRECTORY_SEPARATOR.$alto->attributes('xlink', true)->{'href'};
		copy($old_file, $new_file);
	}

	//----- PDF
	if (!file_exists($wp_upload_dir.DIRECTORY_SEPARATOR.'PDF')) {
	    mkdir($wp_upload_dir.DIRECTORY_SEPARATOR.'PDF', 0777, true);
	}
	$old_pdf = $path.DIRECTORY_SEPARATOR.'PDF'.DIRECTORY_SEPARATOR.'dokumentacja_wuoz.pdf';
	$new_pdf = $wp_upload_dir.DIRECTORY_SEPARATOR.'PDF'.DIRECTORY_SEPARATOR.str_replace(' ', '_', $title).'.pdf';
	copy($old_pdf, $new_pdf);
	$pdf_file_type = wp_check_filetype(basename($new_pdf), null);
	$pdf_attachment_data = array (
		'guid' => $wp_upload_url . '/PDF/' . basename( $new_pdf ),
		'post_mime_type' => $pdf_file_type['type'],
		'post_title' => $title.' ['.$signature.']',
		'post_status' => 'inherit'
	);
	$pdf_attachment = wp_insert_attachment($pdf_attachment_data, $new_pdf);
	echo "[AT]".$pdf_attachment."[/AT]\n";
	$pdf_attach_data = wp_generate_attachment_metadata( $pdf_attachment, $new_pdf );
	wp_update_attachment_metadata( $pdf_attachment,  $pdf_attach_data );

	//----- DjVu
	if (!file_exists($wp_upload_dir.DIRECTORY_SEPARATOR.'DjVu')) {
	    mkdir($wp_upload_dir.DIRECTORY_SEPARATOR.'DjVu', 0777, true);
	}
	$djvu_path=$path.DIRECTORY_SEPARATOR."DjVu";
	$zip = new ZipArchive;
	$new_zip = $wp_upload_dir.DIRECTORY_SEPARATOR.'DjVu'.DIRECTORY_SEPARATOR.str_replace(' ', '_', $title).'_djvu.zip';
	$zip->open($new_zip, ZipArchive::CREATE);
	if (false !== ($dir = opendir($djvu_path))) {
		while (false !== ($file = readdir($dir))) {
			if ($file != '.' && $file != '..') {

				$zip->addFile($djvu_path.DIRECTORY_SEPARATOR.$file);
			}
		}

		$zip->close();
		$zip_file_type = wp_check_filetype(basename($new_zip), null);
		$zip_attachment_data = array(
			'guid' => $wp_upload_url . '/DjVu/' . basename( $new_zip ),
			'post_mime_type' => $zip_file_type['type'],
			'post_title' => $title.' ['.$signature.'] (DjVu)',
			'post_status' => 'inherit'
		);

		$zip_attachment = wp_insert_attachment($zip_attachment_data, $new_zip);
		echo "[AT]".$zip_attachment."[/AT]\n";
		$zip_attach_data = wp_generate_attachment_metadata( $zip_attachment, $new_zip );
		wp_update_attachment_metadata( $zip_attachment,  $zip_attach_data );
	}

	$post_args = array(
		'post_title' => $title.'',
		'post_type' => 'document',
		'post_content' => ' ',
		'post_status'   => 'draft',
	);

	$post_id = wp_insert_post($post_args, true);
	echo "[POST]".$post_id."[/POST]\n";
	set_post_thumbnail($post_id, $jpeg_ids[0]);

	$creator = $xml_contents->xpath('/mets:mets/mets:dmdSec/mets:mdWrap/mets:xmlData/oai_dc:dc/dc:creator');
	$date = $xml_contents->xpath('/mets:mets/mets:dmdSec/mets:mdWrap/mets:xmlData/oai_dc:dc/dc:date');
	$type = $xml_contents->xpath('/mets:mets/mets:dmdSec/mets:mdWrap/mets:xmlData/oai_dc:dc/dc:type');
	$rights = $xml_contents->xpath('/mets:mets/mets:dmdSec/mets:mdWrap/mets:xmlData/oai_dc:dc/dc:rights');
	$description1 = $xml_contents->xpath("/mets:mets/mets:dmdSec/mets:mdWrap/mets:xmlData/oai_dc:dc/dc:description[not(@*)]");
	$description2 = $xml_contents->xpath("/mets:mets/mets:dmdSec/mets:mdWrap/mets:xmlData/oai_dc:dc/dc:description[@xml:lang='pl']");

	foreach($description1 as $d1) {
		$description .= (string)$d1.'<br/>';
	}
	foreach($description2 as $d2) {
		$description .= (string)$d2.'<br/>';
	}


	update_post_meta($post_id, 'oz_jpegs', json_encode($jpeg_ids));
	update_post_meta($post_id, 'oz_pdf', $pdf_attachment);
	update_post_meta($post_id, 'oz_djvu', $zip_attachment);
	update_post_meta($post_id, 'oz_document_signature', $signature);
	update_post_meta($post_id, 'oz_document_creator', (string)$creator[0]);
	update_post_meta($post_id, 'oz_document_date', (string)$date[0]);
	update_post_meta($post_id, 'oz_document_type', (string)$type[0]);
	update_post_meta($post_id, 'oz_document_rights', (string)$rights[0]);
	update_post_meta($post_id, 'oz_document_description', $description.'');


	if($monument_id!=-1)
		connect_mon_doc($monument_id, $post_id);

	$f = fopen($path.DIRECTORY_SEPARATOR.'.imported', w);
	fclose($f);

	echo "[RESULT]ok[/RESULT]";

	die();
}
if(is_admin()) {
	add_action('wp_ajax_upload_handler', 'otwarte_upload_handler');
	add_action('wp_ajax_nopriv_upload_handler', 'otwarte_upload_handler');
}
function otwarte_upload_handler() {
	$path = $_REQUEST['path'];
	$type = $_REQUEST['type'];
	$signature = $_REQUEST['signature'];
	$monument_id = $_REQUEST['monument'];
	$params = array(
		'action' => 'upload_document',
		'path' => $path,
		'type' => $type,
		'signature' => $signature,
		'monument' => $monument_id);

	$query = http_build_query ($params);

	$contextData = array (
		'method' => 'POST',
		'header' => "Connection: close\r\n".
		            "Content-Length: ".strlen($query)."\r\n",
		'content'=> $query );

	$context = stream_context_create (array ( 'http' => $contextData ));

	$result = file_get_contents(admin_url( 'admin-ajax.php' ), false, $context);

	if(get_string_between($result, '[RESULT]', '[/RESULT]')=='ok') {
		echo "OK";
	}
	else {
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $result) as $line){
    		if(get_string_between($line, '[AT]', '[/AT]')!='') {
    			wp_delete_attachment(get_string_between($line, '[AT]', '[/AT]'), true);
    		}
    		else if (get_string_between($line, '[POST]', '[/POST]')!='') {
    			wp_delete_post(get_string_between($line, '[POST]', '[/POST]'), true);
    		}
		}
		echo $result;
	}
	die();
}
