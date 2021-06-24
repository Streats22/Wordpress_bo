<?php if ( ! defined( '\ABSPATH' ) ) exit;



///////////////////////////////////////////////////////////////////////////
//  Guess Items without any value given e.g. from the current context    //
///////////////////////////////////////////////////////////////////////////



/**
 * Tries to get an Entity by the current Wordpress context
 *
 * this function primarily calls the filter. functionality is hooked to this filter to allow plugin authors to
 * work with priorities
 *
 * @param  mixed $entity
 * @return false|object false or Item
 *
 * @since 1.0.0
 */

function sfw_maybe_get_entity_item_by_context( $entity ) {

  $entity = sfw_get_entity( $entity );

  //bail
  if( !sfw_is_entity( $entity ) )
    return false;

  /**
   * Get post or term for given Entity by context
   *
   * @param $item Entity item if already found, else false
   * @param $entity
   *
   * @since 1.0.0
   */

  $item = apply_filters( 'sfw/maybe_get_entity_item_by_context', false, $entity );

  return $entity->is_valid_item( $item )
    ? $item
    : false;
}


add_filter( 'sfw/maybe_get_entity_item_by_context', '_sfw_hook_maybe_get_entity_by_taxonomy_edit_screen', 30,  2 );
add_filter( 'sfw/maybe_get_entity_item_by_context', '_sfw_hook_maybe_get_entity_by_post_edit_screen',     30,  2 );
add_filter( 'sfw/maybe_get_entity_item_by_context', '_sfw_hook_maybe_get_item_by_loop',                   40,  2 );
add_filter( 'sfw/maybe_get_entity_item_by_context', '_sfw_hook_maybe_get_entity_item_by_related_entity',  50,  2 );
add_filter( 'sfw/maybe_get_entity_item_by_context', '_sfw_hook_maybe_get_item_by_queried_object',         100, 2 );
add_filter( 'sfw/maybe_get_entity_item_by_context', '_sfw_hook_maybe_get_item_by_global_post',            120, 2 );




/**
 * Try to guess Entity Item by queried Object
 *
 * @see HOOK sfw_maybe_get_entity_item_by_context
 * @ignore
 *
 * @since 1.0.0
 */

function _sfw_hook_maybe_get_item_by_queried_object( $item, $entity ) {

  if( false !== $item )
    return $item;

  $object = get_queried_object();

  // check the object
  if( $entity->is_valid_item( $object ) ) {
    $item = $object;
  }

  return $item;
}




/**
 * Try to guess Entity Item by Taxonomy Edit Screen
 *
 * @see HOOK sfw_maybe_get_entity_item_by_context
 * @ignore
 *
 * @since 1.0.0
 */

function _sfw_hook_maybe_get_entity_by_taxonomy_edit_screen( $item, $entity ) {

  if( false !== $item OR !$entity->is_term() )
    return $item;

  // if admin edit screen
  if( sfw_current_screen_id_is( 'edit-'.sfw_get_entity_taxonomy( $entity ) ) && !empty( $_REQUEST['tag_ID'] ) ) {
    $item = get_term( $_REQUEST['tag_ID'] );
  }

  return $item;
}




/**
 *  Try to guess Entity Item by Post Edit Screen
 *
 * @see HOOK sfw_maybe_get_entity_item_by_context
 * @ignore
 *
 * @since 1.0.0
 */

function _sfw_hook_maybe_get_entity_by_post_edit_screen( $item, $entity ) {

  if( false !== $item OR !$entity->is_post() )
    return $item;

  // if admin edit screen
  if( sfw_current_screen_id_is( sfw_get_entity_posttype( $entity ) ) && !empty( $_REQUEST['post'] ) ) {
    $item = get_post( $_REQUEST['post'] );
  }

  return $item;
}




/**
 *  Try to guess Entity Item by the current item in the loop
 *
 * @see HOOK sfw_maybe_get_entity_item_by_context
 * @ignore
 *
 * @since 1.0.0
 */

function _sfw_hook_maybe_get_item_by_loop( $item, $entity ) {

  if( false !== $item OR !$entity->is_post() )
    return $item;

  if( in_the_loop() ) {

    $post = get_post( get_the_ID() );

    if( $entity->is_valid_item( $post ) ) {
      $item = $post;
    }
  }

  return $item;
}




/**
 * Try to guess Entity Item by the current global $post
 *
 * @see HOOK sfw_maybe_get_entity_item_by_context
 * @ignore
 *
 * @since 1.0.0
 */

function _sfw_hook_maybe_get_item_by_global_post( $item, $entity ) {

  if( false !== $item OR !$entity->is_post() )
    return $item;

  if( get_the_ID() ) {

    $post = get_post( get_the_ID() );

    if( $entity->is_valid_item( $post ) ) {
      $item = $post;
    }
  }

  return $item;
}




/**
 * Try to guess the item, when guess of other Entities works
 *
 * @see HOOK sfw_maybe_get_entity_item_by_context
 * @ignore
 *
 * @since  1.0.0
 */

function _sfw_hook_maybe_get_entity_item_by_related_entity( $item, $entity ) {

  if( false !== $item )
    return $item;

  // for these entities check article
  if( $entity->is( [ 'producttype', 'design', 'brand' ] ) ) {

    $article_post = sfw_maybe_get_entity_item_by_context( 'article' );

    if( sfw_is_wp_post( $article_post ) ) {

      $maybe_item = sfw_maybe_get_entity_item_by_value( $entity, $article_post );

      if( $entity->is_valid_item( $maybe_item ) )
        $item = $maybe_item;
    }

  }

  return $item;
}




//////////////////////////////////////////
// Guess Entity Items by a given value  //
//////////////////////////////////////////




/**
 * Try to retrieve an Entity Item by a given value
 *
 * @param  mixed $entity An Entity
 * @param  mixed Any non false
 *
 * @return false|object false or Item
 *
 * @since 1.0.0
 */

function sfw_maybe_get_entity_item_by_value( $entity, $mixed ) {

  $entity = sfw_get_entity( $entity );

  //bail
  if( !sfw_is_entity( $entity ) OR $mixed === false )
    return false;

  /**
   * Try to guess an Entity Item from a value
   *
   * @param $item Entity item if already found, else false
   * @param $entity
   * @param mixed $value Any value
   */

  $item = apply_filters( 'sfw/maybe_get_entity_item_by_value', false, $entity, $mixed );

  return $entity->is_valid_item( $item )
    ? $item
    : false;
}

add_filter( 'sfw/maybe_get_entity_item_by_value', 'sfw_maybe_get_entity_item_by_wp_object', 100, 3 );
add_filter( 'sfw/maybe_get_entity_item_by_value', 'maybe_get_entity_item_by_related_item',  100, 3 );




/**
 * Checks if value is valid entity item
 *
 * @see HOOK sfw_maybe_get_entity_item_by_value
 * @ignore
 *
 * @since 1.0.0
 */

function sfw_maybe_get_entity_item_by_wp_object( $item, $entity, $mixed ){

  if( false !== $item )
    return $item;

  if( $entity->is_valid_item( $mixed ) )
    $item = $mixed;

  return $item;
}




/**
 * Get Entity item by another related Entity item
 *
 * @see HOOK sfw_maybe_get_entity_item_by_value
 * @ignore
 *
 * @since 1.0.0
 */

function maybe_get_entity_item_by_related_item( $item, $entity, $mixed ) {

  if( false !== $item )
    return $item;


  // for these entities check article
  if( $entity->is( array( 'producttype', 'design', 'brand' ) ) ) {


    if( !sfw_is_valid_item( 'article', $mixed ) )
      return $item;

    $article_post = $mixed;

    // singular taxonomies
    if( $entity->is( array( 'producttype', 'brand' ) ) ) {

      $term = sfw_get_primary_term( sfw_get_entity_taxonomy( $entity ), $article_post );

      if( $entity->is_valid_item( $term ) )
        $item = $term;
    }

    // default design id
    elseif( $entity->is( 'design' ) && function_exists( 'sfw_get_article_default_design_id' ) ) {

      $design_id = sfw_get_article_default_design_id( $article_post );

      $design = sfw_get_item( 'design', $design_id );

      if( $entity->is_valid_item( $design ) )
        $item = $design;
    }

  }

  return $item;
}






///////////////////////
// getter functions  //
///////////////////////



/**
 * try to guess an Entity Item by optional $value
 *
 * if $mixed has any value other than false we explicitly guess by this value. this is to
 * prevent false positives by context guessing
 *
 * @param  mixed  $entity An Entity
 * @param  mixed  $mixed Any value that identifies an Entity Item or false for automatic Entity guessing
 *
 * @return false|object Will return false if no Item was found, else returns the Item
 *
 * @since 1.0.0
 */

function sfw_maybe_get_entity_item( $entity, $mixed = false ) {

  // try to get $item from context
  if( false === $mixed )
    return sfw_maybe_get_entity_item_by_context( $entity );
  else
    return sfw_maybe_get_entity_item_by_value( $entity, $mixed );
}




/**
 * tries to get Spreadshirt Id for Item
 *
 * @param  mixed  $entity An Entity
 * @param  mixed  $mixed Any value that identifies an Entity Item or false for automatic Entity guessing
 *
 * @return false|object Will return false if no Item was found, else returns the Items Spreadshirt Id
 *
 * @since 1.0.0
 */

function sfw_maybe_guess_entity_spreadshirt_id( $entity, $mixed = false ){

  if( maybe_is_spreadshirt_id( $mixed ) )
    return $mixed;

  $item = sfw_maybe_get_entity_item( $entity, $mixed );

  return sfw_is_valid_item( $entity, $item )
    ? sfw_get_spreadshirt_id_by_item( $entity, $item )
    : false;
}

