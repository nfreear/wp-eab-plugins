<?php // namespace Nfreear\WP_EAB_Plugins;

/**
 * Plugin Name: EAB Archive shortcode
 * Plugin URI:  https://github.com/nfreear/wp-eab-plugins
 * Description: [eab_archive] shortcode for the E-Access Bulletin.
 * Author:      Nick Freear
 * Author URI:  https://github.com/nfreear
 * Version:     1.0-alpha
 *
 * @package Nfreear\WP_EAB_Plugins
 * @copyright © Nick Freear, 10-March-2018.
 * @link  http://headstar.com/eablive/?page_id=1419
 * @link  http://headstar.com/eablive/?eab_bulletin=february-2018
 * @link  https://codex.wordpress.org/Class_Reference/WP_Query#Date_Parameters
 */

class Eab_Archive_Shortcode_Plugin {

	const SHORTCODE = 'eab_archive';

	// const POST_TYPE  = 'eab_bulletin';
	const WP_QUERY   = 'post_type=eab_bulletin&year=2018';
	const JSON_URL   = 'http://headstar.com/eab/index.json';
	const START_YEAR = 2000;
	const SWITCH_LT_YEAR = 2018;

	const IFRAME_URL = 'http://headstar.com/eab/archive.html?embed=1&site=eablive&hide-year-nav-etc=1';
	const IFRAME_TPL = '<iframe src="{u}" width="100%" height="{h}" class="eab-archive-ifr" style="border:0"></iframe>';
	const IFRAME_HEIGHT = 5400;

	// 'E-Access Bulletin – Issue 197, February 2018'
	const TITLE_REGEX = '/.*Issue (?P<issue>[12]\d{2}),? (?P<date>(?P<mo>\w+) (?P<yr>20\d{2}))/';

	public function __construct() {
		// add_action( 'init', [ &$this, 'init' ]);

		add_shortcode( self::SHORTCODE, array( &$this, 'shortcode' ) );
	}

	public function shortcode( $atts, $content = null ) {
		self::debug( array( self::SHORTCODE, $atts, $content ) );

		return
			'<div class="eab_archive">' .
			self::top_nav() . self::wp_query() . self::legacy_archive() .
			'</div>';
			// self::legacy_iframe();
	}

	protected static function wp_query() {
		$is_current = true;
		ob_start();

		// https://wordpress.stackexchange.com/questions/154624/how-do-i-get-content-of-custom-post-type-through-post-id-in-wordpress
		// 'post_type=movie_reviews&ID=244'
		$my_query = new WP_Query( self::WP_QUERY );

		?>
		<div id="y2018" class="wp"><h3>2018</h3><ul class="year">
		<?php

		while ( $my_query->have_posts() ) :
			$my_query->the_post();

			if ( ! preg_match( self::TITLE_REGEX, get_the_title(), $matches ) ) {
				self::error( 'regex problem [eab_archive]' );
				break;
			}

			$pm = (object) $matches;

			self::debug( $pm )
			?>
			<li id="i<?php echo $pm->issue; ?>"
				<?php echo $is_current ? 'title="current"><i id="current"></i>' : '>'; ?>
				Issue <?php echo $pm->issue; ?>, <a href="<?php the_permalink(); ?>" class="htm"
				title="<?php the_title(); ?>"><?php echo $pm->date; ?> HTML</a>.</li>
<?php
			// the_content();
			$is_current = false;
		endwhile;

		?>
		</ul></div>
		<?php

		return ob_get_clean();
	}

	protected static function top_nav() {
		ob_start();
		require_once __DIR__ . '/template/archive-top-nav.tpl.php';
		return ob_get_clean();
	}

	protected static function end_year() {
		return (int) date( 'Y' );
	}

	protected static function legacy_filter( $year ) {
		return ( $year < self::SWITCH_LT_YEAR );
	}

	protected static function legacy_archive() {
		$resp = wp_remote_get( self::JSON_URL, array(
			'timeout' => 15,
			'httpversion' => '1.1',
		) );
		if ( is_wp_error( $resp ) ) {
			return self::error( 'HTTP. ' . $resp->get_error_message() );
		}
		$http_code = $resp['response']['code']; // wp_remote_retrieve_response_code( $resp );
		if ( 200 !== $http_code ) {
			return self::error( 'HTTP, ' . $resp['response']['message'] );
		}
		$eab_archive = json_decode( $resp['body'] );
		if ( ! $eab_archive ) {
			return self::error( 'HTTP. Missing data ' . $http_code );
		}

		ob_start();
		require_once __DIR__ . '/template/eab-archive.tpl.php';
		return ob_get_clean();
	}

	protected static function legacy_iframe() {
		return strtr( self::IFRAME_TPL, array(
			'{u}' => self::IFRAME_URL,
			'{h}' => self::IFRAME_HEIGHT,
		) );
	}

	protected static function lurl( $year, $file ) {
		echo "http://headstar.com/eab/issues/$year/$file";
	}

	protected static function lname( $year, $mon_num ) {
		echo date( 'F Y', mktime( null, null, null, $mon_num + 1, null, $year ) );
	}

	protected static function debug( $obj ) {
		?>
		<!-- <?php print_r( $obj ); ?> -->
		<?php
	}

	protected static function error( $msg ) {
		echo " <i class='error' style='color:red'>Error. $msg</i> ";
		return null;
	}

	/*
	<iframe
		style="border: 0;"
		width="100%"
		height="5400"
		src="http://headstar.com/eab/archive.html?embed=1"
	></iframe>
	*/
}

$plugin = new Eab_Archive_Shortcode_Plugin();

// End.
