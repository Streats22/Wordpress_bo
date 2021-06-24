<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Get all Appearances
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_appearances( $producttype_selector = false ) {

  return !empty( $producttype = sfw_get_producttype( $producttype_selector ) )
    ? $producttype->appearances
    : false;
}




/**
 * Get a Appearance Loop
 *
 * @uses Sfw_Node_Loop
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_appearance_loop( $producttype_selector = false ) {

  $producttype_id = sfw_get_producttype_id( $producttype_selector );


  $found = false;
  $appearance_loop = wp_cache_get( $producttype_id, 'appearance-loop', false, $found );

  if( !$found ) {

    $producttype = sfw_get_producttype( $producttype_id );

    $appearance_loop = !empty( $producttype )
      ? new Sfw_Node_Loop( $producttype->appearances )
      : false;

    wp_cache_set( $producttype_id, $appearance_loop,  'appearance-loop' );
  }

  return $appearance_loop;
}




/**
 * Iterate through Appearances. Use it like have_posts
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_have_appearances( $producttype_selector = false ) : bool {


  $producttype_id = sfw_get_producttype_id( $producttype_selector );

  if( empty( $appearance_loop = sfw_get_appearance_loop( $producttype_id ) ) )
    return false;


  $have = $appearance_loop->have_nodes();

  // the loop changed, so refresh cache
  wp_cache_set( $producttype_id, $appearance_loop,  'appearance-loop' );


  return $have;
}




/**
 * Check if the Appearance Loop is runnig
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_appearances_in_the_loop( $producttype_selector = false ) : bool {

  return !empty( $appearance_loop = sfw_get_appearance_loop( $producttype_selector ) )
    ? $appearance_loop->in_the_loop()
    : false;
}




/**
 * Get the current Appearance
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_current_appearance( $producttype_selector = false ) {

  $appearance_loop = sfw_get_appearance_loop( $producttype_selector );

  return sfw_appearances_in_the_loop( $producttype_selector )
    ? $appearance_loop->current_node()
    : false;
}




/**
 * Get either the current Appearance or a specified Appearance of the current ProductType
 *
 * @param  boolean $appearance_id
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_appearance( $appearance_id = false ) {

  if( empty( $appearance_id ) ) {

    return sfw_get_current_appearance();
  }

  return sfw_search_array_node( sfw_get_appearances(), 'id', $appearance_id );
}




/**
 * Get the Id of the current Appearance
 *
 * @param  boolean $appearance_id
 *
 * @return false|int
 *
 * @since 1.0.0
 */

function sfw_get_appearance_id( $appearance_id = false ) {

  if( !empty( $appearance_id ) ) {

    //
  }
  elseif( sfw_appearances_in_the_loop() ) {

    $appearance_id = sfw_get_current_appearance()->id;
  }
  elseif( ! empty( $producttype = sfw_get_producttype() ) ) {

    $appearance_id = $producttype->appearances[0]->id;
  }
  else {

    $appearance_id = false;
  }

  return $appearance_id;
}




/**
 * Echo the Id of the current Appearance
 *
 * @since 1.0.0
 */

function sfw_appearance_id(  ) {

  echo sfw_get_appearance_id(  );
}




/**
 * Get the Name of the current or specified Appearance
 *
 * @param  boolean $appearance_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_appearance_name( $appearance_id = false, $producttype_selector = false  ) {

  return !empty( $appearance = sfw_get_appearance( $appearance_id, $producttype_selector ) )
    ? apply_filters('sfw/appearance/name', (string) $appearance->name, $appearance )
    : false;
}




/**
 * [sfw_appearance_name description]
 *
 * @param  boolean $appearance_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_appearance_name( $appearance_id = false, $producttype_selector = false ) {

  echo sfw_get_appearance_name( $appearance_id, $producttype_selector );
}




/**
 * Get the Icon Source Url of the current or specified Appearance
 *
 * @param  boolean $appearance_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_appearance_icon_src( $appearance_id = false, $producttype_selector = false  ) {

  return !empty( $appearance = sfw_get_appearance( $appearance_id, $producttype_selector ) )
    ? apply_filters('sfw/appearance/iconsrc', (string) $appearance->resources[0]->href, $appearance )
    : false;
}




/**
 * Echo the Icon Source Url of the current or specified Appearance
 *
 * @param  boolean $appearance_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_appearance_icon_src( $appearance_id = false, $producttype_selector = false ) {

  echo sfw_get_appearance_icon_src( $appearance_id, $producttype_selector );
}




