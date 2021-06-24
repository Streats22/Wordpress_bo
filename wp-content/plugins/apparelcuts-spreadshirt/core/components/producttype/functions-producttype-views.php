<?php if ( ! defined( '\ABSPATH' ) ) exit;





/**
 * Get all Views
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_views( $producttype_selector = false ) {

  return !empty( $producttype = sfw_get_producttype( $producttype_selector ) )
    ? $producttype->views
    : false;
}




/**
 * Get a View Loop
 *
 * @uses Sfw_Node_Loop
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return object
 *
 * @since 1.0.0
 */

function sfw_get_view_loop( $producttype_selector = false ) {

  $producttype_id = sfw_get_producttype_id( $producttype_selector );


  $found = false;
  $view_loop = wp_cache_get( $producttype_id, 'view-loop', false, $found );

  if( !$found ) {

    $producttype = sfw_get_producttype( $producttype_id );


    $view_loop = !empty( $producttype )
      ? new Sfw_Node_Loop( $producttype->views )
      : false;

    wp_cache_set( $producttype_id, $view_loop,  'view-loop' );
  }

  return $view_loop;
}




/**
 * Iterate through Views. Use it like have_posts
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_have_views( $producttype_selector = false ) : bool {

  $producttype_id = sfw_get_producttype_id( $producttype_selector );

  if( empty( $view_loop = sfw_get_view_loop( $producttype_id ) ) )
    return false;


  $have = $view_loop->have_nodes();

  // the loop changed, so refresh cache
  wp_cache_set( $producttype_id, $view_loop,  'view-loop' );


  return $have;
}




/**
 * Check if the View Loop is runnig
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_views_in_the_loop( $producttype_selector = false ) : bool {

  return !empty( $view_loop = sfw_get_view_loop( $producttype_selector ) )
    ? $view_loop->in_the_loop()
    : false;
}




/**
 * Get the current View
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_current_view( $producttype_selector = false ) {

  $view_loop = sfw_get_view_loop( $producttype_selector );

  return sfw_views_in_the_loop( $producttype_selector )
    ? $view_loop->current_node()
    : false;
}




/**
 * Get either the current View or a specified view of the current ProductType
 *
 * @param  boolean $view_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_view( $view_id = false, $producttype_selector = false ) {

  if( empty( $view_id ) ) {

    return sfw_get_current_view( $producttype_selector );
  }

  return sfw_search_array_node( sfw_get_views( $producttype_selector ), 'id', $view_id );
}




/**
 * Get the Id of the current view
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|int
 *
 * @since 1.0.0
 */

function sfw_get_view_id( $producttype_selector = false ) {

  return !empty( $view = sfw_get_view( $producttype_selector ) )
    ? (int)$view->id
    : false;
}




/**
 * Echoes the Id of the current view
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_view_id( $producttype_selector = false ) {

  echo sfw_get_view_id( $producttype_selector );
}




/**
 * Get the Name of the current or specified View
 *
 * @param  boolean $view_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_view_name( $view_id = false, $producttype_selector = false ) {

  return !empty( $view = sfw_get_view( $view_id, $producttype_selector ) )
    ? apply_filters('sfw/view/name', (string) $view->name )
    : false;
}




/**
 * Echoes the Name of the current or specified View
 *
 * @param  boolean $view_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_view_name( $view_id = false, $producttype_selector = false ) {

  echo sfw_get_view_name( $view_id, $producttype_selector );
}



