<?php // namespace Nfreear\WP_Plugins;

/**
 * Plugin Name: Phpinfo Admin page
 * Plugin URI:  https://github.com/nfreear/wp-eab-plugins
 * Description: ...
 * Author:      Nick Freear
 * Author URI:  https://github.com/nfreear
 * Version:     1.0-alpha
 *
 * @package Nfreear\WP_Plugins
 * @copyright © Nick Freear, 10-March-2018.
 * @link https://codex.wordpress.org/Adding_Administration_Menus
 */

class Ndf_Phpinfo_Admin_Plugin {

	const ID = 'phpinfo-admin';

	public function __construct() {
		/** Step 2 (from text above). */
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}

	public function admin_menu( $atts, $content = null ) {
		add_options_page( 'Phpinfo', 'Phpinfo', 'manage_options', self::ID, array( &$this, 'options_page' ) );
	}

	public function options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$phpinfo = self::get_phpinfo();
		$phpinfo = self::clean_phpinfo( $phpinfo );

		echo sprintf( '%s <div class="%s">%s</div>', self::stylesheet(), self::ID, $phpinfo );
	}

	protected static function get_phpinfo() {
		ob_start();
		phpinfo();
		return ob_get_clean();
	}

	protected static $clean = array(
		'<title>phpinfo()</title>',
		'<html><head>',
		'</head>',
		'</html>',
		'<body>',
		'</body>',
	);

	protected static function clean_phpinfo( $phpinfo ) {
		$phpinfo = preg_replace( '/<style.*<\/style>/ms', '', $phpinfo );
		$phpinfo = preg_replace( '/<!D[^>]+>/', '', $phpinfo );
		$phpinfo = str_replace( self::$clean, '', $phpinfo );
		return $phpinfo;
	}

	protected static function stylesheet() {
		ob_start();
		?>

<style>
/* body {background-color: #fff; color: #222; font-family: sans-serif;} */
%pr pre {margin: 0; font-family: monospace;}
%pr a:link {color: #009; text-decoration: none; background-color: #fff;}
%pr a:hover {text-decoration: underline;}
%pr table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
%pr .center {text-align: center;}
%pr .center table {margin: 1em auto; text-align: left;}
%pr .center th {text-align: center !important;}
%pr td, th {border: 1px solid #666; /*font-size: 75%;*/ vertical-align: baseline; padding: 4px 5px;}
%pr h1 {font-size: 150%;}
%pr h2 {font-size: 125%;}
%pr .p {text-align: left;}
%pr .e {background-color: #ccf; width: 300px; font-weight: bold;}
%pr .h {background-color: #99c; font-weight: bold;}
%pr .v {background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
%pr .v i {color: #999;}
%pr img {float: right; border: 0;}
%pr hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
</style>

<?php
		$styles = ob_get_clean();

		return strtr( $styles, array( '%pr' => ( '.' . self::ID ) ) );
	}
}

$plugin = new Ndf_Phpinfo_Admin_Plugin();

// End.
