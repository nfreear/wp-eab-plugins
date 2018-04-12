<?php
/**
 * Plugin Name: PhpList shortcode
 * Plugin URI:  https://github.com/nfreear/wp-eab-plugins
 * Description: Embed a phpList subscribe form, via a WordPress shortcode - [phplist] Intro text... [/phplist]
 * Author:      Nick Freear
 * Author URI:  https://github.com/nfreear
 * Version:     1.0.0-alpha
 *
 * @package     Nfreear\WP_Plugins
 * @license     https://nfreear.mit-license.org MIT License
 * @copyright   © Nick Freear, 03-, 05-April-2018.
 */

class PhpList_Shortcode_Plugin {

	const SHORTCODE = 'phplist';

	const ID       = 1;
	const URL      = 'https://lists.headstar.com/?p=subscribe&id=%s&embed=wp';
	const LABEL    = 'Subscribe to the Bulletin.';
	const TEMPLATE = "%s\n<div class='phplist-embed'>\n<div>%s</div>\n<iframe src='%s' scrolling='no' title='%s'></iframe>\n</div>";

	public function __construct() {
		add_shortcode( self::SHORTCODE, array( &$this, 'shortcode' ) );
	}

	public function shortcode( $attrs = array(), $content = null ) {
		$input = (object) shortcode_atts(
			array(
				'id'  => self::ID,
				'url' => self::URL,
				'label' => self::LABEL,
			), $attrs
		);

		$iframe_url   = sprintf( $input->url, $input->id );
		$iframe_embed = sprintf( self::TEMPLATE, self::stylesheet(), $content, $iframe_url, $input->label );

		return $iframe_embed;
	}

	protected static function stylesheet() {
		return <<<EOS
<style>
  .phplist-embed { line-height: 1.5em; position: relative; }
  .phplist-embed div { background: #fff; color: #222; height: 99px; width: 100%; position: relative; z-index: 10; }
  .phplist-embed iframe { border: 0; overflow: hidden; height: 575px; width: 100%; position: relative; top: -99px; }
</style>
EOS;
	}
}

$wp_plugin = new PhpList_Shortcode_Plugin();

// End.
