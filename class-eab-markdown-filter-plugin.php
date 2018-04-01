<?php // namespace Nfreear\WP_EAB_Plugins;

/**
 * Plugin Name: EAB-Markdown Lite
 * Plugin URI:  https://github.com/nfreear/wp-eab-plugins
 * Description: Filter to convert HTML headings, paragraphs and links to Markdown.
 * Author:      Nick Freear
 * Author URI:  https://github.com/nfreear
 * Version:     1.0-alpha
 * Text Domain: eab-bulletin
 *
 * @package Nfreear\WP_EAB_Plugins
 * @copyright © Nick Freear, 12-, 31-March-2018.
 *
 * @link  https://github.com/thephpleague/html-to-markdown
 * @link  https://github.com/nfreear/html-to-markdown
 */

class Eab_Markdown_Filter_Plugin {

	const FILTER = 'the_content_markdown';

	const WORD_WRAP = 76;

	public function __construct() {
		add_filter( self::FILTER, array( &$this, self::FILTER ) );
	}

	public function the_content_markdown( $content ) {
		// global $wp;

		$content = self::convert_headings_atx( $content );

		$content = self::convert_links( $content );

		$content = self::convert_paragraphs_etc( $content );

		return wordwrap( $content, self::WORD_WRAP );
	}

	protected static function convert_headings_atx( $content ) {
		return preg_replace_callback(
			'/<h(?P<level>[1-6]).*?>(?P<heading>.+?)<\/h\d>/',
			array( __CLASS__, 'atx_headings_callback' ),
			/* function ( $matches ) {
				return "\n" . str_repeat( '#', $matches['level'] ) . ' ' . $matches['heading'] . "\n";
			}, */
			$content
		);
	}

	protected static function atx_headings_callback( $matches ) {
		return "\n" . str_repeat( '#', $matches['level'] ) . ' ' . $matches['heading'] . "\n";
	}

	public static function convert_links( $content ) {
		return preg_replace_callback(
			'/<a href="(?P<url>.+)">(?P<text>.+?)<\/a>/',
			array( __CLASS__, 'links_callback' ),
			/* function ( $matches ) {
				$url = str_replace( '&amp;', '&', $matches['url'] );
				return '[' . $matches['text'] . '](' . $url . ')';
			}, */
			$content
		);
	}

	protected static function links_callback( $matches ) {
		$url = str_replace( '&amp;', '&', $matches['url'] );
		return '[' . $matches['text'] . '](' . $url . ')';
	}

	protected static function convert_paragraphs_etc( $content ) {

		$content = str_replace( '</p>', "\n", $content );

		$content = preg_replace( '/<(p|\/?div|br).*?>/', '', $content );

		return $content;
	}
}

$plugin = new Eab_Markdown_Filter_Plugin();
