<?php // namespace Nfreear\WP_EAB_Plugins;

/**
 * Plugin Name: EAB Post Type
 * Plugin URI:  https://github.com/nfreear/wp-eab-plugins#post-type
 * Description: The custom "post type" for the E-Access Bulletin.
 * Author:      Nick Freear
 * Author URI:  https://github.com/nfreear
 * Version:     1.0.0-alpha
 * Text Domain: eab-bulletin
 *
 * @package Nfreear\WP_EAB_Plugins
 * @copyright © Nick Freear, 06-November-2017.
 *
 * @link  https://gist.github.com/nfreear/3fecaac8059cc351583c6e8f50d1cf7c
 * @link  https://github.com/IET-OU/wp-juxtalearn-hub/blob/master/post-types/student_problem.php
 * @link  http://headstar.com/eab/issues/2017/oct2017.html
 */

class Bulletin_Post_Type_Plugin {

	const POST_TYPE    = 'eab_bulletin';
	const ARCHIVE_SLUG = 'issues/';
	const EDITOR_URL   = '/wp-admin/post.php?post=%s&action=edit';
	const ISSUE_FIELD  = 'eab_issue_num';
	const LOC_DOMAIN   = 'eab-bulletin';

	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		add_action( 'save_post', array( &$this, 'save_post' ) );
	}

	public function admin_enqueue_scripts() {
		wp_register_style( 'eab_admin_css', plugins_url( 'src/eab-admin.css', __FILE__ ) );
		wp_enqueue_style( 'eab_admin_css' );

		$in_footer = true;
		wp_register_script( 'eab_admin_js', plugins_url( 'src/eab-admin.js', __FILE__ ), array(), false, $in_footer );
		wp_enqueue_script( 'eab_admin_js' );
	}

	public function init() {
		$tx_long   = __( 'E-Access Bulletin', 'eab-bulletin' );
		$tx_name   = __( 'EAB Bulletin', 'eab-bulletin' );
		$tx_plural = __( 'EAB Bulletins', 'eab-bulletin' );

		register_post_type(
			self::POST_TYPE, array(
				'labels'           => array(
					'name'               => $tx_plural,
					'singular_name'      => $tx_name,
					'add_new'            => _x( 'Add New', 'Add new EAB Bulletin', 'eab-bulletin' ),
					// translators: 'Add new E-Access Bulletin'.
					'add_new_item'       => sprintf( __( 'Add new %s', 'eab-bulletin' ), $tx_long ),
					'edit_item'          => sprintf( __( 'Edit %s', 'eab-bulletin' ), $tx_long ),
					'new_item'           => sprintf( __( 'New %s', 'eab-bulletin' ), $tx_long ),
					'view_item'          => sprintf( __( 'View %s', 'eab-bulletin' ), $tx_name ),
					'search_items'       => sprintf( __( 'Search %s', 'eab-bulletin' ), $tx_plural ),
					'not_found'          => sprintf( __( 'No %s found', 'eab-bulletin' ), $tx_plural ),
					'not_found_in_trash' => sprintf( __( 'Not found in Trash: %s', 'eab-bulletin' ), $tx_long ),
				),
				'description'      => __( 'An E-Access Bulletin.', 'eab-bulletin' ),
				'public'           => true,
				'supports'         => array(
					'title',
					'editor',
					/* 'excerpt',  'author', */ 'revisions',
				),
				'has_archive'      => true,
				'delete_with_user' => false,
				'rewrite'          => array(
					'slug'       => self::ARCHIVE_SLUG . strtolower( date( 'Y' ) ),
					'with_front' => false,
				),
				'capability_type'  => 'post',
				'show_ui'          => true,
				'menu_position'    => 5,
				// 'menu_icon' => EAB_HUB_URL.'/images/icons/example.png',
			)
		);
		/* IMPORTANT: Only use once if you have too, see important note at the top of the page for details.  */
		// flush_rewrite_rules( false );
	}

	public function admin_init() {
		add_meta_box(
			self::ISSUE_FIELD, 'Bulletin issue number', array( &$this, 'issue_num_meta' ), self::POST_TYPE, 'side', 'low'
		);
	}

	public function issue_num_meta() {
		?>
		<label>Issue number
		<input name="eab_issue_num" value="<?php echo self::get_issue_num(); ?>"
			placeholder="'111'" pattern="\d{3,4}" data-type="number" title="A number, e.g. '195'" />
		</label>

		<p><hr />
		<ul class="eab-editor-links" role="navigation" aria-label="Bulletin editor links">
		<li><a href="<?php echo self::email_url(); ?>" target="_blank">HTML email (new window)</a></li>
		<li><a href="<?php echo self::email_url( false ); ?>" target="_blank" title="Markdown">Text email (new window)</a></li>
		<li><a href="<?php echo self::edit_template_url(); ?>">Edit Bulletin template</a></li>
		</ul>
		<?php

		self::print_template_json();
	}

	public function save_post() {
		global $post;

		update_post_meta( $post->ID, self::ISSUE_FIELD, filter_input( INPUT_POST, self::ISSUE_FIELD ) );
	}

	// ======================================================

	protected static function print_template_json() {
		?>
	<script id="eab-admin-json" type="application/json">
	<?php echo json_encode( self::get_template(), JSON_PRETTY_PRINT ); ?>

	</script>
	<?php
	}

	protected static function get_template() {
		$post = get_post( self::template_post_id() );

		return array(
			'post_id'           => self::template_post_id(),
			'use_template'      => ! ! $post,
			'template'          => $post->post_content,
			'template_title'    => $post->post_title,
			'template_date'     => $post->post_date,
			'default_title'     => date( 'F Y' ),
			'default_name'      => strtolower( date( 'F' ) ),
			'slug'              => '/' . self::ARCHIVE_SLUG . strtolower( date( 'Y/M' ) ),
			'site_url'          => get_site_url(),
			'edit_template_url' => self::edit_template_url(),
			'html_email_url'    => self::email_url(),
		);
	}

	protected static function edit_template_url() {
		return sprintf( self::EDITOR_URL, self::template_post_id() );
	}

	protected static function template_post_id() {
		return constant( 'EAB_TEMPLATE_ID' );
	}

	protected static function email_url( $is_html = true ) {
		global $post;

		$view_url = sprintf( 'view/?post_id=%d&f=%s', $post->ID, $is_html ? 'html' : 'txt' ); // Was: '&format='

		return plugins_url( $view_url, __FILE__ );
	}

	protected static function get_issue_num() {
		$custom_data = get_post_custom();

		return $custom_data[ self::ISSUE_FIELD ][0];
	}
}

$wp_post_type_plugin = new Bulletin_Post_Type_Plugin();
