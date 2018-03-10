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
 */

class Eab_Archive_Shortcode_Plugin {

	const SHORTCODE = 'eab_archive';

	const POST_TYPE  = 'eab_bulletin';
	const WP_QUERY   = 'post_type=eab_bulletin&';
	const JSON_URL   = 'http://headstar.com/eab/index.json';
	const IFRAME_URL = 'http://headstar.com/eab/archive.html?embed=1&site=eablive&hide=year-nav-etc';
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

		// return $content;
		return
			'<div class="eab_archive">' .
			self::wp_query() . self::legacy_iframe() .
			'</div>';
	}

	protected static function wp_query() {
		ob_start();

		// https://wordpress.stackexchange.com/questions/154624/how-do-i-get-content-of-custom-post-type-through-post-id-in-wordpress
		// 'post_type=movie_reviews&ID=244'
		$my_query = new WP_Query( self::WP_QUERY );

		?>
		<h3 id="a2018">2018</h3><ul class="year">
		<?php

		while ( $my_query->have_posts() ) :
			$my_query->the_post();

			if ( ! preg_match( self::TITLE_REGEX, get_the_title(), $matches ) ) {
				echo ' <i class="error" style="color:red">Error [eab_archive] regex</i> ';
				break;
			}

			$pm = (object) $matches;

			self::debug( $pm )
			?>
			<li>Issue <?php echo $pm->issue; ?>, <a href="<?php the_permalink(); ?>" class="htm"
				title="<?php the_title(); ?>"><?php echo $pm->date; ?> HTML</a></li>
<?php
			// the_content();
		endwhile;

		?>
		</ul>
		<?php

		return ob_get_clean();
	}

	protected static function legacy_iframe() {
		return strtr( self::IFRAME_TPL, array(
			'{u}' => self::IFRAME_URL,
			'{h}' => self::IFRAME_HEIGHT,
		) );
	}

	protected static function debug( $obj ) {
		?>
		<!-- <?php print_r( $obj ); ?> -->
		<?php
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
