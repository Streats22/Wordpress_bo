<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
* show articles by meta _design-id in edit.php screen. this allows the user to easiely find
* all articles with a design
*
* @ignore
* @since 1.0.0
*/

function _sfw_hook_meta_filter_sfw_product_by_design_id( $query ) {

	if( !is_admin() ||  !$query->is_main_query() || !isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'sfw-product' )
		return $query;


	if( isset( $_GET['_design-id'] ) ) {

    $design_id = sanitize_key( $_GET['_design-id'] );
    $search = array( $design_id );

    $children = sfw_get_design_children( $design_id );

    if( $children ) foreach( $children as $_post )
      $search[] = get_post_meta( $_post->ID, sfw_get_entity_wp_metakey('design'), true );

		$query->set( 'meta_key', '_design-id' );
		$query->set( 'meta_value', $search );
		$query->set( 'meta_compare', 'IN' );

	}

	return $query;

}

add_filter( 'pre_get_posts', '_sfw_hook_meta_filter_sfw_product_by_design_id' );




/*
 * Prepare public query
 */

add_action( 'wp', function(){

	if( !is_singular( 'sfw-design' ) )
		return;

	$design_id = sfw_get_design_id();


	// this enables paging
  remove_action('template_redirect', 'redirect_canonical');


	// redirect child designs to parent
	if( sfw_is_child_design( $design_id ) ) {

		// redirect
		$parent_post_id = get_post_meta( get_the_ID(), 'sfw-parent-design-post-id', true );

		wp_redirect( get_permalink( $parent_post_id ), 301 );

		die();
	}

});

