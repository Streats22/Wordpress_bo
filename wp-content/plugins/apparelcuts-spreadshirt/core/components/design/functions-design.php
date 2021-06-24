<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
* This File deals mainly with the JSON response for the design resource,
* Posts with a post_type of 'sfw-design' and Terms of the 'sfw-designid' Taxonomy
*
*/





/**
* Retrieve the current Design Object from the global Sfw Object
*
* @return object|false|WP_Error
* @since 1.0.0
*/

function sfw_get_design( $design_selector = false, $flush_cache = false, $transmit_errors = false ){

  $spreadshirt_id = sfw_get_design_id( $design_selector );

  if( !maybe_is_spreadshirt_id( $spreadshirt_id ) )
    return false;

  return sfw_remote_get_cached( array(

    'url'    => sfw_create_shop_request_url( 'designs', $spreadshirt_id ),

    'query_args' => array(
      'fullData' => 'true',
      'locale' => sfw_get_locale(),
    ),

    'filter' => 'design',

    'cache'  => SFW_Remote_Cache_Entity::get_instance( 'design', $spreadshirt_id ),

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));

}




/**
* Retrieve design id
*
* @param WP_Post|int|string $design_selector - could be an Spreadshirt Design Id, WP_Post or 'wildcard'. Leave empty for auto-guessing.
* @return false|string
* @since 1.0.0
*/

function sfw_maybe_get_design_id( $design_selector = false ) {

  if( $design_selector === 'wildcard' ) {
    // this is a quick way to retrieve a Wildcard, but works only with auto-guessing.
    // sfw_get_wildcard_design_id is more powerful
    return sfw_get_wildcard_design_id( false );
  }

 return sfw_maybe_guess_entity_spreadshirt_id( 'design', $design_selector );

}




/**
 * Echoes design id
 *
 * @param  mixed $design_selector see sfw_get_design_id
 * @since  1.0.0
 */

function sfw_design_id( $design_selector = false ) {

	echo sfw_get_design_id( $design_selector );
}




/**
* Retrieve a Design ID
*
* @param WP_Post|int|string $design_selector - could be an Spreadshirt Design Id, WP_Post or 'wildcard'. Leave empty for auto-guessing.
* @return false|string
* @since 1.0.0
*/

function sfw_get_design_id( $design_selector = false ) {

  return sfw_maybe_get_design_id( $design_selector );
}




/**
* Get the Wildcard Design Id
*
* Returns the Id of the Designs Parent if present, otherwise the design id
*
* @param  mixed $design_selector see sfw_get_design_id
* @return false | $design_id
* @since 1.0.0
*/

function sfw_get_wildcard_design_id( $design_selector = false ) {

  return sfw_is_child_design( $design_selector )
    ? sfw_get_parent_design_id( $design_selector )
    : sfw_get_design_id( $design_selector );
}




/**
* Retrieve a Post 'sfw-design' by $design_selector
*
* @see sfw_get_post
* @param  mixed $design_selector see sfw_get_design_id
* @return WP_Post|false
* @since  1.0.0
*/

function sfw_get_design_post( $design_selector = false ) {

  if( empty( $spreadshirt_id = sfw_maybe_get_design_id( $design_selector ) ) )
    return false;

  return sfw_get_post( 'design', $spreadshirt_id );
}




/**
* Get a Designs Parent Id
*
* @param  mixed $design_selector see sfw_get_design_id
* @return false|string
* @since 1.0.0
*/

function sfw_get_parent_design_id( $design_selector = false ) {

  if( empty( $design_post = sfw_get_design_post( $design_selector ) ) )
    return false;

  $_main_design_id  = get_field('sfw-parent-design', $design_post );

  return !empty( $_main_design_id )
    ? $_main_design_id
    : false;
}




/**
* Get a Parent Design
*
* @param  mixed $child_design_selector see sfw_get_design_id
* @return false|object
* @since 1.0.0
*/

function sfw_get_parent_design( $child_design_selector = false ) {

  $parent_design_id = sfw_get_parent_design_id( $child_design_selector );
  return $parent_design_id ? sfw_get_design( $parent_design_id ) : false;
}




/**
* This is a Helper for quickly getting the Parent of a Design if present or
* otherwise the Design itself
*
* @param  mixed $design_selector see sfw_get_design_id
* @return false|string
* @since 1.0.0
*/

function sfw_get_wildcard_design( $design_selector = false ) {

  return sfw_get_design( sfw_get_wildcard_design_id( $design_selector ) );
}




/**
* This is a Helper for quickly getting post of the parent of a Design if present or
* otherwise the designs post itself
*
* @param  mixed $design_selector see sfw_get_design_id
* @return false|WP_Post
* @since 1.0.0
*/

function sfw_get_wildcard_design_post( $design_selector = false ) {

  return sfw_get_design_post( sfw_get_wildcard_design_id( $design_selector ) );
}




/**
* Check if a Design has a Parent
*
* @param  mixed $design_selector see sfw_get_design_id
* @return bool
* @since 1.0.0
*/

function sfw_is_child_design( $design_selector = false ) : bool {

  if( empty( $design_id   = sfw_get_design_id( $design_selector ) ) )
    return false;

  if( empty( $design_post  = sfw_get_design_post( $design_id ) ) )
    return false;

  $parent_design = get_field('sfw-parent-design', $design_post );

  return !empty( $parent_design ) && $parent_design != $design_id;
}




/**
 * Checks if a design probably has $children
 *
 * Relies on the designs article count beeing correct. To be sure use
 * sfw_get_design_children, but be aware that this is expensive.
 *
 * @param  boolean $design_id
 * @return boolean
 * @since  1.0.0
 */

function sfw_design_has_children( $design_id = false ) {
  return sfw_get_design_children_count( $design_id ) > 0;
}




/**
* checks if the current post in the Wordpress Loop is an design
*
* @param $post, optional a post object
* @return true|false
* @since 1.0.0
*/

function sfw_is_design( $post = false ) : bool {

  return $post instanceof WP_Post
    ? get_post_type( $post ) == 'sfw-design'
    : get_post_type( ) == 'sfw-design' || is_singular( 'sfw-design' );
}




/**
* Retrieve the name of the current design
*
* @param  mixed $design_selector see sfw_get_design_id
* @return false|string
* @since 1.0.0
*/

function sfw_get_design_name(  $design_selector = false  ) {

  $value = '';

  if( !empty( $design_post = sfw_get_wildcard_design_post( $design_selector ) ) ){

		$value = apply_filters('sfw/design/name', $design_post->post_title, $design_post );
	}

	return $value;
}




/**
* Echoes the name of the current design
*
* @param  mixed $design_selector see sfw_get_design_id
* @since 1.0.0
*/

function sfw_design_name( $design_selector = false ) {

	echo sfw_get_design_name( $design_selector );
}




/**
* Retrieve the description of the current design
*
* @param  mixed $design_selector see sfw_get_design_id
* @return false|string
* @since 1.0.0
*/

function sfw_get_design_description( $design_selector = false ) {

  return !empty( $design_post = sfw_get_wildcard_design_post( $design_selector ) )
    ? apply_filters( 'sfw/design/description', $design_post->post_content, $design_post )
    : false;
}




/**
* Echoes the description of the current design
*
* @param  mixed $design_selector see sfw_get_design_id
* @since 1.0.0
*/

function sfw_design_description( $design_selector = false ) {

	echo sfw_get_design_description( $design_selector );
}




/**
* Retrieve the backgroundcolor setting of the current design
*
* @param bool $hash Whether or not to prepend a #
* @param  mixed $design_selector see sfw_get_design_id
* @return false|string hexcode
* @since 1.0.0
*/

function sfw_get_design_background_color( $hash = true,  $design_selector = false  ) {

  if( empty( $design_post = sfw_get_design_post( $design_selector ) ) )
    return false;

  $value = get_field( 'sfw-background-color', $design_post );
  $value = $hash ? $value : str_replace( '#', '', $value );

	return apply_filters( 'sfw/design/backgroundcolor', $value, $design_post, $hash );
}




/**
* Echoes the backgroundcolor setting of the current design
*
* @param bool $hash Whether or not to prepend a #
* @param  mixed $design_selector see sfw_get_design_id
* @since 1.0.0
*/

function sfw_design_background_color( $hash = true, $design_selector = false ) {

	echo sfw_get_design_background_color( $hash, $design_selector );
}




/**
* Checks if the design is mainly white
*
* @param  mixed $design_selector see sfw_get_design_id
* @return bool
* @since 1.0.0
*/

function sfw_is_design_white( $design_selector = false ) : bool {

  return !empty( $design_post = sfw_get_design_post( $design_selector ) )
    ? get_field('sfw-iswhite', $design_post )
    : false;
}




/**
* Retrieve the number of articles which are associated to the current design
*
* This will consider articles that are associated with child designs too. Will return 0 in case of an error.
*
* @param  mixed $design_selector see sfw_get_design_id
* @return int
* @since 1.0.0
*/

function sfw_get_design_articles_count( $design_selector = false ){

  $design_id = sfw_get_wildcard_design_id( $design_selector );

  return sfw_get_designs_count_number( '_article-count', $design_id );
}




/**
* Retrieve the number of articles which are associated to the current design
*
* This will not consider articles that are associated with child designs. Will return 0 in case of an error
*
* @param  mixed $design_selector see sfw_get_design_id
* @return int
* @since 1.0.0
*/

function sfw_get_design_articles_total( $design_selector = false ){

  return sfw_get_designs_count_number( '_article-count-total', $design_selector );
}




/**
* Retrieve the number of child designs which are associated to the current design
*
* Will return 0 in case of an error
*
* @param  mixed $design_selector see sfw_get_design_id
* @return int
* @since 1.0.0
*/

function sfw_get_design_children_count( $design_selector = false ){

  return sfw_get_designs_count_number( '_children-count', $design_selector );
}




/**
 * Echoes the designs articles count
 *
 * @see sfw_get_design_articles_count
 * @param  mixed $design_selector see sfw_get_design_id
 * @since  1.0.0
 */

function sfw_design_articles_count( $design_selector = false  ){

  echo sfw_get_design_articles_count( $design_selector );
}




/**
* Retrieve the number of articles which are associated to the current design
*
* Will return 0 in case of an error
*
* @param  mixed $design_selector see sfw_get_design_id
* @return int
* @since 1.0.0
*/

function sfw_get_design_self_articles_count( $design_selector = false ){

  return sfw_get_designs_count_number( '_article-count', $design_id );
}




/**
* Helper for retrieving design count
*
* Will trigger update of the design counts, if count is not set already
*
* @param  string $metakey The metakey containing the count
* @param  mixed $design_selector see sfw_get_design_id
* @return int
* @since 1.0.0
*/

function sfw_get_designs_count_number( $metakey, $design_selector ) {

  $design_post = sfw_get_design_post( $design_selector );

  if( !sfw_is_wp_post( $design_post ) )
    return 0;

  $design_id  = sfw_get_design_id( $design_post );

  $count = get_post_meta( $design_post->ID, $metakey, true );

  // recalc
  if( is_null( $count ) || $count === '' || $count === false ) {

    _sfw_update_design_article_count( $design_id );

    $count = get_post_meta( $design_post->ID, $metakey, true );

  }

  return apply_filters('sfw/design_count_number', intval( $count ), $design_post );
}




/**
* Retrieve the labeled number of articles which are associated to the current design
*
* @param array $labels
* @param  mixed $design_selector see sfw_get_design_id
* @return string
* @since 1.0.0
*/

function sfw_get_design_articles_number( $labels = array(), $design_selector = false ){

  return sfw_numerus(
    is_admin() ? sfw_get_design_articles_total( $design_selector ) : sfw_get_design_articles_count( $design_selector ),
    array(
      __( 'No Articles', 'apparelcuts-spreadshirt' ),
      __( '1 Article', 'apparelcuts-spreadshirt' ),
      __( '%s Articles', 'apparelcuts-spreadshirt' ),
    ),
    $labels
  );
}




/**
* Echoes the labeled number of articles which are associated to the current design
*
* @param array $labels
* @param  mixed $design_selector see sfw_get_design_id
* @return string
* @since 1.0.0
*/

function sfw_design_articles_number( $labels = array(), $design_selector = false  ){

  echo sfw_get_design_articles_number( $labels, $design_selector );
}





/**
* Retrieve the designs permalink by design id
*
* @param  mixed $design_selector see sfw_get_design_id
* @since 1.0.0
*/

function sfw_get_design_permalink( $design_selector = false ) {

  return !empty( $design_post = sfw_get_design_post( $design_selector ) )
    ? get_permalink( $design_post )
    : false;
}




/**
* Echoes the designs permalink by design id
*
* @param  mixed $design_selector see sfw_get_design_id
* @since 1.0.0
*/

function sfw_design_permalink( $design_selector = false ) {
  echo sfw_get_design_permalink( $design_selector );
}




/**
* Retrieve the url to all articles with a design in the Admin Screen
*
* @param int $design_id
* @since 1.0.0
*/

function sfw_get_design_admin_edit_articles_url( $design_id = false ) {

  return !empty( $design_id = sfw_get_design_id( $design_id ) )
    ? esc_url( add_query_arg( array(
        'post_type' => 'sfw-product',
        '_design-id' => $design_id
      ), 'edit.php' ) )
    : false;
}




/**
* retrieve the url to all Articles with a Design in the Admin Screen
*
* @param int $design_id
* @param array $args
* @return string
* @since 1.0.0
*/

function sfw_get_design_admin_edit_articles_link( $design_id = false, $args = array() ) {

  $args = wp_parse_args( $args, array(

    'label' => sfw_get_design_articles_number( array(), $design_id ),

    'href'  => sfw_get_design_admin_edit_articles_url( $design_id )

  ));

  return sfw_get_anchor_tag( $args );
}




/**
* echoes the url to all Articles with a Design in the Admin Screen
*
* @see sfw_get_design_admin_edit_articles_link
* @param int $design_id
* @param array $args
* @since 1.0.0
*/

function sfw_design_admin_edit_articles_link( $design_id = false, $args = array() ) {

  return sfw_get_design_admin_edit_articles_link( $design_id, $args );
}




/**
 * Changes the main query to a designs articles
 *
 * Be careful with this function. It alters the main loop. It should be called before have_posts and you should make sure to either let the loop finish all cycles or call sfw_loop_end_design_articles_query manually.
 *
 * @param  mixed $design_selector optional - see sfw_get_design_id
 * @since  1.0.0
 */

function sfw_design_have_articles( $design_selector = false ) {

	$design_id = sfw_get_design_id( $design_selector );

	$search = array( $design_id );

	if( sfw_design_has_children( $design_id ) ) {

		$children = sfw_get_design_children( $design_id );

		if( $children ) {
			foreach( $children as $_post )
				$search[] = get_post_meta( $_post->ID, sfw_get_entity_wp_metakey('design'), true );
		}
	}


	$args = array(
		'post_type' => 'sfw-product',
		'post_status' => 'publish',
		'meta_key' => '_design-id',
		'meta_compare' => 'IN',
		'meta_value' => $search,
		//'posts_per_page' => 1,
		'paged' => get_query_var( 'paged', 1 ),
	);


	//execute the
	query_posts( $args );

	if( have_posts() ) {

		sfw_var('in_design_article_loop', get_the_ID() );

		add_action( 'loop_end', 'sfw_loop_end_design_articles_query' );

		return true;
	}
	else {

		wp_reset_query();
	}
}




/**
 * Ends design articles loop
 *
 * Detaches itself automatically from lopp_end event.
 *
 * @since  1.0.0
 */

function sfw_loop_end_design_articles_query() {

	sfw_var('in_design_article_loop', false );

	wp_reset_query();

	remove_action( 'loop_end', 'sfw_loop_end_design_articles_query' );
}