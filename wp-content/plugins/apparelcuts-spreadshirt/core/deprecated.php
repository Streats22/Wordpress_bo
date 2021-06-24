<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Deprecated Code will be stored here to reduce broken code
 *
 * @since 1.0.0
 */




/*
 * fix to reset wrong lang caches, will be removed again in a future release
 * @todo safely remove after 8th Mai 2019
*/

add_filter( 'sfw/cache/expired', function( $expired, $cache, $expire ){

  if( $cache instanceof SFW_Remote_Cache_Entity ) {

    $puffer = HOUR_IN_SECONDS;
    $time = 1556629104;

    if( $cache->entity->is( 'producttype' ) ) {

      if( $expire <= ( $time + $puffer + DAY_IN_SECONDS * 3 ) ) {
        return true;
      }
    }
    if( $cache->entity->is( 'product' ) || $cache->entity->is( 'article' ) || $cache->entity->is( 'design' ) ) {

      if( $expire <= ( $time + $puffer + WEEK_IN_SECONDS ) ) {
        return true;
      }
    }
  }

  return $expired;

}, 10, 3 );
