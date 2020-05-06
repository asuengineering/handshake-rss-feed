<?php
/**
 * Plugin Name:     Handshake RSS Feed (ASU Engineering)
 * Plugin URI:      https://github.com/asuengineering/handshake-rss-feed
 * Description:     Embed an RSS feed from <a href="https://www.joinhandshake.com/" target="_blank">Handshake</a> into a WordPress post or page.
 * Author:          Steve Ryan
 * Author URI:      https://engineering.asu.edu
 * Version:         0.1
 *
 * GitHub Plugin URI: https://github.com/asuengineering/handshake-rss-feed
 */

/* TODO: Check to see if Carbon Fields is loaded. If not loaded, go for it using code within plugin. For now, assume it's loaded. */
/* Define theme option fields to collect thw Handshake RSS feed and RSS 2 JSON API key */
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'asefse_add_handshake_rss_options' );
function asefse_add_handshake_rss_options() {

	Container::make( 'theme_options', __( 'Handshake RSS Feed' ) )
	->set_page_parent( 'tools.php' )
    ->add_fields( array(
		Field::make( 'html', 'crb_information_text' )
		->set_html( '<p>These plugin settings effect the output of the <code>[handshake-rss]</code> shortcode. The plugin utilizes the service <a href="https://rss2json.com/#rss_url=https%3A%2F%2Fgithub.com%2Fvuejs%2Fvue%2Freleases.atom" target="_blank" title="RSS 2 JSON">RSS 2 JSON</a>. The free service requires registration and a free API key.</p>' ),
        Field::make( 'text', 'handshake_rss_feed_url', 'Handshake RSS feed URL'),
		Field::make( 'text', 'handshake_rss_api_key', 'RSS 2 JSON API key' ),
		Field::make( 'text', 'handshake_rss_item_count', 'Max # items returned' )
			->set_help_text( 'If left blank, the maximum number of returned items will be 10. Imposed limit by the RSS 2 JSON service.')
	) );

}

/* Register some scripts and stylesheets. Enqueue them during the shortcode callback. */
add_action( 'wp_enqueue_scripts', 'asefse_add_handshake_rss_scripts' );
function asefse_add_handshake_rss_scripts() {

	wp_register_style( 'handshake-rss-css', plugin_dir_url( __FILE__ ) . '/css/handshake-rss.css', array(), null );
	wp_register_script( 'handshake-rss-js', plugin_dir_url( __FILE__ ) . '/js/handshake-rss.js', array('jquery', 'jquery-collapser'), null);
	wp_register_script( 'jquery-collapser', plugin_dir_url( __FILE__ ) . '/js/jquery.collapser.min.js', array('jquery'), '3.0');
}


/* Shortcode callback when the shortcode is encountered on the page. */
function handshake_rss_shortcode() {

	$service = 'https://api.rss2json.com/v1/api.json?rss_url=';

	$rss_url = '';
	$rss_url = carbon_get_theme_option( 'handshake_rss_feed_url' );

	if (!empty( carbon_get_theme_option( 'handshake_rss_api_key' ))) {
		$api_key = '&api_key=' . carbon_get_theme_option( 'handshake_rss_api_key' );
	}

	if (!empty( carbon_get_theme_option( 'handshake_rss_item_count' ))) {
		$item_count = '&count=' . carbon_get_theme_option( 'handshake_rss_item_count' );
	}

	// Empty checks for the theme settings page.
	if ( (empty($rss_url)) || (empty( $api_key)) ) {
		echo 'Please check the Handshake RSS theme options settings. You are missing a value.';
		return false;
	}

	$request = wp_remote_get( $service . $rss_url . $api_key . $item_count );

	// Error check for invalid JSON.
	if ( is_wp_error( $request ) ) {
		return false; // Bail early
	}

	wp_enqueue_style( 'handshake-rss-css' );
	wp_enqueue_script( 'handshake-rss-js' );
	wp_enqueue_script( 'jquery-collapser' );

	$body = wp_remote_retrieve_body( $request );

	$data = json_decode( $body );
	$output = '';

	if( ! empty( $data ) ) {

		foreach( $data->items as $listing ) {
			$description = preg_replace('#\R+#', '<br/>', $listing->description);

			$output .= '<div class="handshake-item">';
			$output .= '<a class="handshake-item-title" href="' . esc_url( $listing->link ) . '">' . $listing->title . '</a>';
			$output .= '<p class="handshake-item-description">' . $description . '</p>';
			$output .= '</div>';
		}

	}

	return $output;

}

/* Register the shortcode. */
function handshake_rss_shortcode_init(){
	add_shortcode( 'handshake-rss', 'handshake_rss_shortcode' );
}
add_action('init', 'handshake_rss_shortcode_init');
