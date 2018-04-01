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
define( 'POST_ID', filter_input( INPUT_GET, 'post_id', FILTER_VALIDATE_INT ) );
define( 'EAB_NAME', filter_input( INPUT_GET, 'n', FILTER_SANITIZE_URL ) );
define( 'EAB_FORMAT', filter_input( INPUT_GET, 'f', FILTER_SANITIZE_URL ) );
define( 'EAB_IS_TEXT', EAB_FORMAT && preg_match( '/^(te?xt|md)$/', EAB_FORMAT ) );

define( 'EAB_POST_TYPE', 'eab_bulletin' );
define( 'NAME_REGEX', '/^[a-z]+\-20\d{2}$/' );

require_once dirname( __FILE__ ) . '/../../../../wp-config.php';

// var_dump( EAB_NAME, POST_ID );

if ( EAB_NAME ) {
	$query_args = array(
		'post_type' => EAB_POST_TYPE,
		'name'      => EAB_NAME,
	);
} elseif ( POST_ID ) {
	$query_args = array(
		'post_type' => EAB_POST_TYPE,
		'p'         => POST_ID,
	);
}

$my_query = new WP_Query( $query_args );

if ( ! $my_query->have_posts() ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( 404 );
	exit;
}

$my_query->the_post();

if ( ! is_user_logged_in() ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 403 );
	get_template_part( 403 );
	exit;
}

header( 'X-Link: ' . get_permalink() );
header( 'X-Guid: ' . get_the_guid() );

if ( EAB_IS_TEXT ) {
	header( 'Content-Type: text/markdown; charset=utf-8' );
	header( 'Content-Disposition: inline; filename=eab-bulletin.md' );

	// echo trim( str_replace( home_url(), '', get_permalink() ), '/' );

	echo "\n# ";
	the_title();
	echo "\n\n";

	echo apply_filters( 'the_content_markdown', get_the_content(), get_permalink() );

	//the_content();
} else {
	require __DIR__ . '/email-template-html.php';
}

// End.
