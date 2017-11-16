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

class EAB_Filter_Render_Plugin {

	const POST_TYPE = 'eab_bulletin';

	const ISSN    = 'ISSN: 1476-6337';
	const TITLE   = 'E-Access Bulletin – Issue {{ISSUE}}, {{TITLE}}';
	const TAGLINE = 'Access to technology for all, regardless of ability.';
  const EMAIL   = 'eaccessbulletin@gmail.com';
	const LIST_URL = 'https://lists.headstar.com';
	const HOME_URL = 'http://headstar.com/eablive';
	const ARCHIVE_URL = 'http://headstar.com/eab/archive.html';

	public function __construct() {
		// add_action( 'init', [ &$this, 'init' ]);

		add_filter( 'the_content', [ &$this, 'the_content_filter' ] );
		add_filter( 'the_title', [ &$this, 'the_title_filter' ] );
	}

	public function the_content_filter( $content ) {
		$issue_num = self::get_issue_num();

		$content = strtr(
			$content, [
				'{{EAB_ISSUE}}'   => 'Issue ' . $issue_num,
				'{{EAB_ISSN}}'    => sprintf( '<em class="issn">%s.</em>', self::ISSN ),
				'{{EAB_TAGLINE}}' => sprintf( '<em class="tagline">%s.</em>', self::TAGLINE ),
				'{{EMAIL}}'       => sprintf( '<a href="mailto:%s">%s</a>', self::EMAIL, self::EMAIL ),
				'{{LIST_URL}}'    => sprintf( '<a href="%s">%s</a>', self::LIST_URL, self::LIST_URL ),
				'{{ARCHIVE_URL}}' => sprintf( '<a href="%s">%s</a>', self::ARCHIVE_URL, self::ARCHIVE_URL ),
			]
		);

		return $content;
	}

	public function the_title_filter( $title ) {

		if ( get_post_type() === self::POST_TYPE ) {
			$title = strtr(
				self::TITLE, [
					'{{TITLE}}' => $title,
					'{{ISSUE}}' => self::get_issue_num(),
				]
			);
		}

		return $title;
	}

	protected static function get_issue_num() {
		global $post;

		$custom_data = get_post_custom( $post->ID );

		return $custom_data['eab_issue_num'][0];
	}
}

$wp_filter_plugin = new EAB_Filter_Render_Plugin();
