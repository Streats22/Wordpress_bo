<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
 * Retrieve the main menu slug
 *
 * @return string the main plugin menu slug
 * @since 1.0.0
 */

function sfw_admin_get_parent_menu_slug() {

	/**
	 * Sets the main page of the plugin
	 *
	 * @var string the main menu slug
	 */

	return apply_filters( 'sfw/parent_menu_slug', null );
}




/**
 * Retrieve the main menu slug
 *
 * @return string the main plugin menu slug
 * @since 1.0.0
 */

function sfw_admin_get_parent_menu_position() {

	/**
	 * Sets the main menu positoon of the plugin
	 *
	 * @var string the main menu slug
	 */

	return apply_filters( 'sfw/parent_menu_position', 45 );
}




/**
 * Retrieve the posttype menu position
 *
 * @return string the main plugin menu slug
 * @since 1.0.0
 */

function sfw_admin_get_posttype_menu_position() {

	/**
	 * Sets the plugins post types menu position
	 *
	 * @var string the main menu slug
	 */

	return apply_filters( 'sfw/posttype_menu_position', sfw_admin_get_parent_menu_position() + 3 );
}




/**
 * Retrieve the Parent Page Url
 *
 * @return string the main admin_url
 * @since 1.0.0
 */

function sfw_admin_get_parent_page_url() {

	return admin_url( 'admin.php?page='.sfw_admin_get_parent_menu_slug() );
}




/**
 * sfw_acf_options_page
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_admin_page_manager() {

	global $sfw_admin_page_manager;

	if( !isset($sfw_admin_page_manager) ) {

		$sfw_admin_page_manager = new SFW_Admin_Page_Manager();

	}

	return $sfw_admin_page_manager;
}




// initialize
sfw_admin_page_manager();




/**
 * add_options_page
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_add_admin_page( $page ) {

	return sfw_admin_page_manager()->add_page( $page );
}




/**
 * sfw_update_admin_page
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_update_admin_page( $slug = '', $data = array() ) {

	return sfw_admin_page_manager()->update_page( $slug, $data );

}




/**
 * get_options_page
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_get_admin_page( $slug ) {

	// vars
	$page = sfw_admin_page_manager()->get_page( $slug );


	// bail early if no page
	if( !$page ) return false;

	return $page;

}




/**
 * get_options_pages
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_get_admin_pages() {

	$pages = sfw_admin_page_manager()->get_pages();

	if( empty($pages) ) return false;

	return $pages;
}




/**
 * sfw_register_admin_page
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_register_admin_page( $page = '' ) {

	sfw_admin_page_manager()->add_page( $page );
}




/**
 * Add metabox to sfw admin page
 *
 * @param string $options_page_slug option page name
 * @param array $args the metabox args
 * @since 1.0.0
 */

function sfw_admin_page_add_metabox( $options_page_slug, $args ) {

	$metabox = wp_parse_args( $args, array(
		'id' 				=> uniqid(),
		'title' 		=> '&nbsp;',
		'classes'   => array(),
		'hidden' 		=> false,
		'context' 	=> 'normal',
		'callback' 	=> null,
		'priority' 	=> 'default',
		'args' 			=> array()
	));

	add_action( 'sfw_admin_page_add_metaboxes', function () use ( $metabox, $options_page_slug ) {

		extract( $metabox );

		add_meta_box(
			$id,
			$title,
			$callback,
			'sfw-admin-page-'.$options_page_slug,
			$context,
			$priority,
			$args
		);

		if( !empty( $classes ) ) {
			add_filter( "postbox_classes_sfw-admin-page-{$options_page_slug}_{$id}", function( $_classes ) use ( $classes ) {

				return array_merge( $_classes, $classes );
			} );
		}

	});

}