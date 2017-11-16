<?php namespace Nfreear\WP_EAB_Plugins;

/**
 * Plugin Name: EAB Post-Type
 * Plugin URI:  https://github.com/nfreear/wp-eab-plugins
 * Description: Post type for the E-Access Bulletin.
 * Author:      Nick Freear
 * Author URI:  https://github.com/nfreear
 * Version:     1.0-alpha
 *
 * @copyright © 2017 Nick Freear.
 * @author    Nick Freear, 06-November-2017.
 *
 * @link  https://gist.github.com/nfreear/3fecaac8059cc351583c6e8f50d1cf7c
 * @link  https://github.com/IET-OU/wp-juxtalearn-hub/blob/master/post-types/student_problem.php
 * @link  http://headstar.com/eab/issues/2017/oct2017.html
 */

class Bulletin_Post_Type_Plugin {

	const POST_TYPE    = 'eab_bulletin';
	const ARCHIVE_SLUG = 'issues';
	const LOC_DOMAIN   = 'eab-bulletin';

	const SINGULAR = 'EAB Bulletin';
	const PLURAL   = 'EAB Bulletins';
	const LONG     = 'E-Access Bulletin';

	const EDITOR_URL = '/wp-admin/post.php?post=%s&action=edit';

	public function __construct() {
		add_action( 'init', [ &$this, 'init' ] );
		add_action( 'admin_init', [ &$this, 'admin_init' ] );
		add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ] );
		add_action( 'save_post', [ &$this, 'save_post' ] );
	}

	public function admin_enqueue_scripts() {
		wp_register_style( 'eab_admin_css', plugins_url( 'src/eab-admin.css', __FILE__ ) ); //get_template_directory_uri() . '/admin-style.css', false, '1.0.0' );
		wp_enqueue_style( 'eab_admin_css' );

		$in_footer = true;
		wp_register_script( 'eab_admin_js', plugins_url( 'src/eab-admin.js', __FILE__ ), [], false, $in_footer );
		wp_enqueue_script( 'eab_admin_js' );
	}

	public function init() {
		register_post_type(
			self::POST_TYPE, [
				/* 'labels' => [
				'name' => __(sprintf('%ss', ucwords(str_replace("_", " ", self::POST_TYPE)))),
				'singular_name' => __(ucwords(str_replace("_", " ", self::POST_TYPE)))
				], */
				'labels'           => [
					'name'               => __( self::PLURAL, self::LOC_DOMAIN ),
					'singular_name'      => __( self::SINGULAR, self::LOC_DOMAIN ),
					'add_new'            => __( 'Add New', self::LOC_DOMAIN ), //sprintf('Add New %s', self::SINGULAR)
					'add_new_item'       => __( sprintf( 'Add new %s', self::LONG ), self::LOC_DOMAIN ),
					'edit_item'          => __( sprintf( 'Edit %s', self::LONG ), self::LOC_DOMAIN ),
					'new_item'           => __( sprintf( 'New %s', self::LONG ), self::LOC_DOMAIN ),
					'view_item'          => __( sprintf( 'View %s', self::SINGULAR ), self::LOC_DOMAIN ),
					'search_items'       => __( sprintf( 'Search %s', self::PLURAL ), self::LOC_DOMAIN ),
					'not_found'          => __( sprintf( 'No %s found', self::PLURAL ), self::LOC_DOMAIN ),
					'not_found_in_trash' => __( sprintf( 'Not found in Trash: %s', self::PLURAL ), self::LOC_DOMAIN ),
				],
				'public'           => true,
				'description'      => __( 'An E-Access Bulletin', self::LOC_DOMAIN ),
				'supports'         => [
					'title',
					'editor',
					/* 'excerpt',  'author', */ 'revisions',
				],
				'has_archive'      => true,
				'delete_with_user' => false,
				'rewrite'          => [
					'slug'       => self::ARCHIVE_SLUG . strtolower( date( '/Y/M' ) ),
					'with_front' => false,
				],
				'capability_type'  => 'post',
				'show_ui'          => true,
				'menu_position'    => 5,
				// 'menu_icon' => JUXTALEARN_HUB_URL.'/images/icons/example.png',
			]
		);
		/* IMPORTANT: Only use once if you have too, see important note at the top of the page for details.  */
		// flush_rewrite_rules( false );
	}

	public function admin_init() {
		add_meta_box( 'eab_issue_num', 'Bulletin issue number', [ &$this, 'issue_num_meta' ], self::POST_TYPE, 'side', 'low' );
	}

	public function issue_num_meta() {
	?>
	<label>Issue number
	<input name="eab_issue_num" value="<?php echo self::get_issue_num(); ?>"
	  placeholder="111" pattern="\d{3,4}" data-type="number" title="A number, e.g. '195'" />
  </label>

	<p><hr />
  <ul id="eab-editor-links">
  <li><a href="<?php echo self::html_email_url(); ?>" target="_blank">HTML email (new window)</a>
  <li><a href="<?php echo self::edit_template_url(); ?>">Edit Bulletin template</a>
  </ul>
	<?php

	  self::print_template_json();
	}

	public function save_post() {
		global $post;
		update_post_meta( $post->ID, 'eab_issue_num', $_POST['eab_issue_num'] );
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

		return [
			'post_id'           => self::template_post_id(),
			'use_template'      => ! ! $post,
			'template'          => $post->post_content,
			'template_title'    => $post->post_title,
			'template_date'     => $post->post_date,
			'default_title'     => date( 'F Y' ),
			'slug'              => '/' . self::ARCHIVE_SLUG . strtolower( date( '/Y/M' ) ) . '.html',
			'site_url'          => get_site_url(),
			'edit_template_url' => self::edit_template_url(),
			'html_email_url'    => self::html_email_url(),
		];
	}

	protected static function edit_template_url() {
		return sprintf( self::EDITOR_URL, self::template_post_id() );
	}

	protected static function template_post_id() {
		return constant( 'EAB_TEMPLATE_ID' );
	}

	protected static function html_email_url( $post_id = null ) {
		global $post;

		return plugins_url( 'email/?post_id=', __FILE__ ) . $post->ID;
	}

	protected static function get_issue_num() {
		global $post;

		$custom_data = get_post_custom( $post->ID );

		return $custom_data['eab_issue_num'][0];
	}
}

$wp_post_type_plugin = new Bulletin_Post_Type_Plugin();
