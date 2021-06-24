<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Retrieve the shops language data
 *
 * @param  int $spreadshirt_id
 * @param  boolean $flush_cache
 * @param  boolean $transmit_errors
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_language( $spreadshirt_id = false, $flush_cache = false, $transmit_errors = false ){

  $spreadshirt_id = sfw_get_language_id( $spreadshirt_id );

  if( empty( $spreadshirt_id ) ) return false;

  $cache = $spreadshirt_id === sfw_get_language_id()
    ? new SFW_Remote_Cache_Option_Autoload( 'language' )
    : null; // default to transient cache

  $data = sfw_remote_get_cached( array(

    'url'     => sfw_create_request_url( 'languages', $spreadshirt_id ),

    'filter'  => 'language',

    'expire'  => WEEK_IN_SECONDS,

    'cache'   => $cache,

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));

  // make sure we don't load the wrong data from the options cache.
  if( $cache && !$flush_cache && is_spreadshirt_object( $data ) && $data->id != $spreadshirt_id )
      return sfw_get_language( $spreadshirt_id, true );

  return $data;
}




/**
 * Retrieve language id
 *
 * @param  boolean $spreadshirt_id
 *
 * @return int language id
 *
 * @since 1.0.0
 */

function sfw_get_language_id( $spreadshirt_id = false ) {

  $language_id = false === $spreadshirt_id
    ? sfw_get_shop()->language->id
    : $spreadshirt_id;

  return apply_filters( 'sfw/language/id', $language_id, $spreadshirt_id );
}
