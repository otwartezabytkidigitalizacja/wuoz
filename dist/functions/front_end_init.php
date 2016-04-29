<?php

function header_scripts_styles() {

	wp_register_style(
		'foundation-app',
		get_template_directory_uri() . "/stylesheets/app.css"
	);

	wp_register_style(
		'ubuntu',
		"http://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700&subset=latin,latin-ext"
	);

	wp_enqueue_style( 'ubuntu' );
	wp_enqueue_style( 'foundation-app' );

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'jquery-ui-droppable' );
	wp_enqueue_script( 'jquery-ui-resizable' );

}

add_action( 'wp_enqueue_scripts', 'header_scripts_styles' );

function enqueue_custom_js() {
	wp_enqueue_script( 'otwarte-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.get_maps_api_key().'&sensor=false&v=3.13' );
	wp_enqueue_script( 'grunt-output', get_template_directory_uri() . "/javascripts/scripts.min.js", array( 'jquery' ), '1.0',  true );
	wp_localize_script( 'grunt-output', 'otwarte_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'template_url' => get_template_directory_uri() ) );


}

add_action( 'wp_enqueue_scripts', 'enqueue_custom_js' );

function contact_map( $atts ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'lat' => '52.25196',
			'lng' => '20.99753',
		), $atts )
	);

	// Code
	return '<div id="contact-map" data-lat="'.$lat.'" data-lng="'.$lng.'"></div>';
}
add_shortcode( 'mapa-kontakt', 'contact_map' );

function favicon(){
	echo "<link rel='shortcut icon' href='" . get_stylesheet_directory_uri() . "/favicon.ico' />";
}

add_action("wp_head", "favicon");