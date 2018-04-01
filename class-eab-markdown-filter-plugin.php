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
 * @link
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

		$content = self::convert_headings( $content );

		$content = self::convert_links( $content );

		$content = self::convert_paragraphs_etc( $content );

		return wordwrap( $content, self::WORD_WRAP );
	}

	protected static function convert_headings( $content ) {
		return preg_replace_callback(
			'/<h(?P<level>[1-6]).*?>(?P<heading>.+?)<\/h\d>/',
			function ( $matches ) {
				return "\n" . str_repeat( '#', $matches['level'] ) . ' ' . $matches['heading'] . "\n";
			},
			$content
		);
	}

	protected static function convert_links( $content ) {
		return preg_replace_callback(
			'/<a href="(?P<url>.+)">(?P<text>.+?)<\/a>/',
			function ( $matches ) {
				$url = str_replace( '&amp;', '&', $matches['url'] );
				return '[' . $matches['text'] . '](' . $url . ')';
			},
			$content
		);
	}

	protected static function convert_paragraphs_etc( $content ) {

		$content = str_replace( '</p>', "\n", $content );

		$content = preg_replace( '/<(p|\/?div|br).*?>/', '', $content );

		return $content;
	}
}

$plugin = new Eab_Markdown_Filter_Plugin();
