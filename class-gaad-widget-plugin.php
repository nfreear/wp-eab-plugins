<?php // namespace Nfreear\WordPress\Plugins\GAAD;

/**
 * Plugin Name: Global Accessibility Awareness Day widget
 * Plugin URI:  https://github.com/nfreear/gaad-widget
 * Description: Shortcode and functionality to embed the GAAD widget ~ http://globalaccessibilityawarenessday.org
 * Author:      Nick Freear
 * Author URI:  https://twitter.com/nfreear
 * Version:     3.1.0-beta
 *
 * @package     Nfreear\WP_GAAD_Plugins
 * @license     https://nfreear.mit-license.org MIT License
 * @copyright   © 2017 Nick Freear, 14-May-2017.
 * @link        https://gist.github.com/nfreear/e5520adbb930e537ef5fe2e0aab231d1#
 * @link        http://globalaccessibilityawarenessday.org/
 */

class GAAD_Widget_Plugin {

	const SHORTCODE  = 'GAAD';
	const TEMPLATE   = '<div id="id-gaad-widget"></div>';
	const VERSION    = '3.1.0-beta';
	const SCRIPT_URL = 'https://unpkg.com/gaad-widget@%s/build/gaad-widget.js';

	private $wp_head = false;

	public function __construct() {
		$this->wp_head = self::get_option( 'gaad_widget_wp_head' );

		add_shortcode( self::SHORTCODE, array( &$this, 'shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_filter( 'script_loader_tag', array( &$this, 'script_loader_tag' ), 10, 2 );

		if ( $this->wp_head ) {
			add_action( 'wp_head', [ &$this, 'wp_head' ] );
		}
	}

	public function wp_head( $name ) {
		self::debug( array( __FUNCTION__, $name ) );

		echo self::TEMPLATE;
	}

	public function shortcode( $attrs = [], $content = null ) {
		$inp = (object) shortcode_atts(
			array(
				'days_before' => self::get_option( 'gaad_widget_days_before', 15 ),
				'days_after'  => self::get_option( 'gaad_widget_days_after', 10 ),
			), $attrs
		);

		return self::TEMPLATE . $content;
	}

	public function enqueue_scripts() {
		$version    = self::get_option( 'gaad_widget_version', self::VERSION );
		$script_url = sprintf( self::SCRIPT_URL, $version );

		self::debug(
			array(
				'fn'                  => __FUNCTION__,
				'gaad_widget_version' => $version,
				'wp_default_theme'    => self::get_option( 'wp_default_theme' ),
			)
		);

		$ver = null;
		$in_footer = true;
		$result = wp_enqueue_script( 'gaad-widget', $script_url, array( 'jquery' ), $ver, $in_footer );

		if ( ! $this->wp_head ) {
			$tpl = addslashes( self::TEMPLATE );
			// wp_enqueue_script( 'gaad-widget-inj', '_404_#-gaad-widget-xxx.js', [ 'jquery' ], $ver = false, $in_footer = true );
			$position = 'before';
			wp_add_inline_script( 'gaad-widget', "jQuery('#masthead').after('$tpl');console.warn('WP:gaad-widget')", $position );
		}
		return $result;
	}

	public function script_loader_tag( $tag, $handle ) {
		if ( 'gaad-widget' === $handle ) {
			$json_opt = json_encode(
				[
					'days_before' => self::get_option( 'gaad_widget_days_before', 15 ),
					'days_after'  => self::get_option( 'gaad_widget_days_after', 10 ),
					'theme'       => self::get_option( 'gaad_widget_theme', 'blue' ),
					'client'      => 'WordPress',
				]
			);

			return str_replace( '></sc', " data-gaad='$json_opt'><\/sc", $tag );
			// return str_replace( '<script', "<script data-gaad='$json_opt'", $tag );
		}
		return $tag;
	}

	public static function get_option( $option, $default = false ) {
		$opt     = strtoupper( $option );
		$default = defined( $opt ) ? constant( $opt ) : $default;
		return get_option( $option, $default );
	}

	public static function debug( $obj ) {
		static $count = 0;
		$str          = sprintf( 'X-gaad-widget-plugin-%02d: %s', $count, json_encode( $obj ) );
		if ( headers_sent() ) {
			echo "<!-- $str -->";
		} else {
			header( $str );
		}
		$count++;
	}
}

$wp_plugin = new GAAD_Widget_Plugin();

// End.
