<?php namespace Nfreear\WP_EAB_Plugins;

/**
 * Plugin Name: EAB Filters
 * Plugin URI:  https://github.com/nfreear/wp-eab-plugins
 * Description: Filters and rendering for the E-Access Bulletin.
 * Author:      Nick Freear
 * Author URI:  https://github.com/nfreear
 * Version:     1.0-alpha
 *
 * @copyright © 2017 Nick Freear, 11-November-2017.
 * @link  http://headstar.com/eab/issues/2017/oct2017.html
 */

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
} else {
	require_once __DIR__ . '/../../../vendor/autoload.php';
}

use League\HTMLToMarkdown\HtmlConverter;

class EAB_Filter_Render_Plugin {

	const POST_TYPE = 'eab_bulletin';
	const TEXT_WRAP = 76;

	const ISSN        = 'ISSN: 1476-6337';
	const TITLE       = 'E-Access Bulletin – Issue {{ISSUE}}, {{TITLE}}';
	const TAGLINE     = 'Access to technology for all, regardless of ability.';
	const TOC_LINK    = '<p class="Goto"><a href="#toc">Contents</a>.</p>';
	const EMAIL       = 'eaccessbulletin@gmail.com';
	const TEN_URL     = 'http://headstar.com/ten';
	const LIST_URL    = 'https://lists.headstar.com';
	const HOME_URL    = 'http://headstar.com/eablive';
	const ARCHIVE_URL = 'http://headstar.com/eablive/?p=1419';  // '?page_id=1419'
	// const ARCHIVE_URL = 'http://headstar.com/eab/archive.html';

	protected static $is_text = false;

	public function __construct() {
		// add_action( 'init', [ &$this, 'init' ]);

		add_filter( 'the_content', [ &$this, 'the_content_filter' ] );
		add_filter( 'the_title', [ &$this, 'the_title_filter' ] );

		$format = filter_input( INPUT_GET, 'format', FILTER_SANITIZE_URL );

		self::$is_text = $format && preg_match( '/^(te?xt|md)$/', $format );
	}

	public function the_content_filter( $content ) {
		$issue_num = self::get_issue_num();

		$content = strtr(
			$content, [
				'{{EAB_ISSUE}}'    => 'Issue ' . $issue_num,
				'{{EAB_ISSN}}'     => sprintf( '<em class="issn">%s.</em>', self::ISSN ),
				'{{EAB_TAGLINE}}'  => sprintf( '<em class="tagline">%s.</em>', self::TAGLINE ),
				'{{EMAIL}}'        => sprintf( '<a href="mailto:%s">%s</a> ', self::EMAIL, self::EMAIL ),
				'{{TEN_LINK}}'     => self::link( 'TEN_URL' ),
				'{{HOME_LINK}}'    => self::link( 'HOME_URL' ),
				'{{LIST_LINK}}'    => self::link( 'LIST_URL' ),
				'{{ARCHIVE_LINK}}' => self::link( 'ARCHIVE_URL' ),
				'{{TOC_LINK}}'     => self::TOC_LINK,
			]
		);

		$content = self::readable_html_links( $content );

		$content = self::html_to_markdown( $content );

		return $content;
	}

	public function the_title_filter( $title ) {

		if ( self::is_bulletin() ) {
			$title = strtr(
				self::TITLE, [
					'{{TITLE}}' => $title,
					'{{ISSUE}}' => self::get_issue_num(),
				]
			);
		}

		return $title;
	}

	// ======================================================

	protected static function link( $key ) {
		$url = constant( 'self::' . $key );
		return sprintf( '<a href="%s">%s</a> ', $url, $url );
	}

	protected static function html_to_markdown( $content ) {
		if ( self::$is_text && self::is_bulletin() ) {
			$converter = new HtmlConverter(
				[
					'strip_tags'   => true,
					'header_style' => 'atx',
				]
			);

			$markdown = $converter->convert( $content );

			return wordwrap( $markdown, self::TEXT_WRAP );
		}

		return $content;
	}

	protected static function readable_html_links( $content ) {
		if ( ! self::$is_text ) {
			$content = preg_replace(
				'/<(a[^>]+)>https?:\/\/([^<]+?)\/?<\/a>/', '<$1>$2</a>', $content
			);
		}

		return $content;
	}

	protected static function is_bulletin() {
		return get_post_type() === self::POST_TYPE;
	}

	protected static function get_issue_num() {
		$custom_data = get_post_custom();

		return $custom_data['eab_issue_num'][0];
	}
}

$wp_filter_plugin = new EAB_Filter_Render_Plugin();
