<?php namespace Nfreear\WP_EAB_Plugins;

/**
 * Plugin Name: EAB Filters
 * Plugin URI:  https://github.com/nfreear/wp-eab-plugins
 * Description: Filters and rendering for the E-Access Bulletin.
 * Author:      Nick Freear
 * Author URI:  https://github.com/nfreear
 * Version:     1.0-alpha
 *
 * @copyright © 2017 Nick Freear.
 * @author    Nick Freear, 11-November-2017.
 *
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
	const ARCHIVE_URL = 'http://headstar.com/eab/archive.html';

	protected $is_text = false;

	public function __construct() {
		// add_action( 'init', [ &$this, 'init' ]);

		add_filter( 'the_content', [ &$this, 'the_content_filter' ] );
		add_filter( 'the_title', [ &$this, 'the_title_filter' ] );

		$format = filter_input( INPUT_GET, 'format', FILTER_SANITIZE_URL );

		$this->is_text = $format && preg_match( '/^(te?xt|md)$/', $format );
	}

	public function the_content_filter( $content ) {
		$issue_num = self::get_issue_num();

		$content = strtr(
			$content, [
				'{{EAB_ISSUE}}'    => 'Issue ' . $issue_num,
				'{{EAB_ISSN}}'     => sprintf( '<em class="issn">%s.</em>', self::ISSN ),
				'{{EAB_TAGLINE}}'  => sprintf( '<em class="tagline">%s.</em>', self::TAGLINE ),
				'{{EMAIL}}'        => sprintf( '<a href="mailto:%s">%s</a> ', self::EMAIL, self::EMAIL ),
				'{{TEN_LINK}}'     => sprintf( '<a href="%s">%s</a> ', self::TEN_URL, self::TEN_URL ),
				'{{HOME_LINK}}'    => sprintf( '<a href="%s">%s</a> ', self::HOME_URL, self::HOME_URL ),
				'{{LIST_LINK}}'    => sprintf( '<a href="%s">%s</a> ', self::LIST_URL, self::LIST_URL ),
				'{{ARCHIVE_LINK}}' => sprintf( '<a href="%s">%s</a> ', self::ARCHIVE_URL, self::ARCHIVE_URL ),
				'{{TOC_LINK}}'     => self::TOC_LINK,
			]
		);

		if ( $this->is_text && self::is_bulletin() ) {
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

	protected static function is_bulletin() {
		return get_post_type() === self::POST_TYPE;
	}

	protected static function get_issue_num() {
		$custom_data = get_post_custom();

		return $custom_data['eab_issue_num'][0];
	}
}

$wp_filter_plugin = new EAB_Filter_Render_Plugin();
