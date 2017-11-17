<?php

/**
 * Output a view of a Bulletin, suitable for a HTML email.
 *
 * @copyright © 2017 Nick Freear.
 * @author  Nick Freear, 12 November 2017.
 *
 * @link  /wp-content/plugins/wp-eab-bulletin/email-html/?post_id=49
 */

define( 'WP_USE_THEMES', false );

require_once __DIR__ . '/../../../../wp-config.php';

if ( ! is_user_logged_in() ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 403 );
	get_template_part( 403 );
	exit;
}

$post_id = filter_input( INPUT_GET, 'post_id', FILTER_VALIDATE_INT );
$format  = filter_input( INPUT_GET, 'format', FILTER_SANITIZE_URL );
$is_text = $format && preg_match( '/^(te?xt|md)$/', $format );

$post = get_post( $post_id );

if ( ! $post ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( 404 );
	exit;
}

$wp_query = new WP_Query();
$result   = $wp_query->setup_postdata( $post );

// var_dump( $result, $post );

header( 'X-Link: ' . get_permalink() );
// header( 'X-Guid: ' . get_the_guid() );

if ( $is_text ) {
	header( 'Content-Type: text/markdown; charset=utf-8' );
	header( 'Content-Disposition: inline; filename=eab-bulletin.md' );

	// echo trim( str_replace( home_url(), '', get_permalink() ), '/' );

	echo '# ';
	the_title();
	echo "\n\n";

	the_content();
} else {
	require __DIR__ . '/email-template-html.php';
}

// End.
