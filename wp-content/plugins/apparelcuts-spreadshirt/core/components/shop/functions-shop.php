<?php if ( ! defined( '\ABSPATH' ) ) exit;





/**
* Retrieve the main Shop Data
*
* @param int|string $spreadshirt_id A Spreadshirt Shop Id
* @param bool $flush_cache If set to true, the data will always be requested from the remote host
* @param bool $transmit_errors Set to true, to receive WP_Error instead of false in case of errors
*
* @return object|false|WP_Error Shopdata on success or false if the data could not be retrieved.
*
* @since 1.0.0
*/

function sfw_get_shop( $spreadshirt_id = false, $flush_cache = false, $transmit_errors = false ){

  $spreadshirt_id = sfw_get_shop_id( $spreadshirt_id );


  if( empty( $spreadshirt_id ) ) return false;

  $cache = $spreadshirt_id === sfw_get_shop_id()
    ? new SFW_Remote_Cache_Option_Autoload( 'shop' )
    : null; // default to transient cache


  $data = sfw_remote_get_cached( array(

    'url'     => sfw_create_request_url( 'shops', $spreadshirt_id ),

    'filter'  => 'shop',

    'expire'  => WEEK_IN_SECONDS,

    'cache'   => $cache,

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));

  // make sure we don't load the wrong data from the options cache. this
  // is in case the shop id changed
  if( $cache && !$flush_cache && is_spreadshirt_object( $data ) && $data->id != $spreadshirt_id )
      return sfw_get_shop( $spreadshirt_id, true );

  return $data;
}




/**
* Retrieve a Shop Id
*
* This is an alias of sfw_get_activated_shop_id()
*
* @see sfw_get_activated_shop_id
*
* @since 1.0.0
*/

function sfw_get_shop_id( $placeholder = false ) {

  return sfw_get_activated_shop_id();
}




/**
 * Echo the Shop Id
 *
 * @see sfw_get_shop_id
 *
 * @since  1.0.0
 */

function sfw_shop_id() {

  echo sfw_get_shop_id();
}




/**
* Check if the current host is Spreadshirt.net
*
* @return bool
*
* @since 1.0.0
*/

function sfw_is_net() : bool {

  return sfw_get_host() === 'net';
}




/**
* Check if the current host is Spreadshirt.com
*
* @return bool
*
* @since 1.0.0
*/

function sfw_is_com() : bool {

  return sfw_get_host() === 'com';
}




/**
* Returns one of two values depending on the cvrrent host
*
* @param mixed $net The value that should be returned for Spreadshirt.net
* @param mixed $com The value that should be returned for Spreadshirt.com
*
* @return mixed One of the given values
*
* @since 1.0.0
*/

function sfw_net_or_com( $net, $com ) {

  return sfw_is_net() ? $net : $com;
}


