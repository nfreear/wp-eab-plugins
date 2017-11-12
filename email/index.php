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

// global $post;

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

// var_dump( $result );
// if ( have_posts() ) : while ( have_posts() ) : the_post();

// var_dump( $post );

// $post->post_title_filtered = apply_filters( 'the_title', $post->post_title );
// $post->post_content_filtered = apply_filters( 'the_content', $post->post_content );

// echo $post->post_title_filtered;
// echo $post->post_content_filtered;

echo '<h1 style="color: darkorange">[ A work in progress! ]</h1>';

the_title();
the_content();

// endwhile; endif;

// End.
