<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
 * Retrieve the curreny resource
 *
 * @param  boolean $spreadshirt_id
 * @param  boolean $flush_cache
 * @param  boolean $transmit_errors
 *
 * @return object|false|WP_Error
 *
 * @since 1.0.0
 */

function sfw_get_currency( $spreadshirt_id = false, $flush_cache = false, $transmit_errors = false ){

  $spreadshirt_id = sfw_get_currency_id( $spreadshirt_id );

  if( empty( $spreadshirt_id ) ) return false;

  $cache = $spreadshirt_id === sfw_get_currency_id()
    ? new SFW_Remote_Cache_Option_Autoload( 'currency' )
    : null; // default to transient cache

  return sfw_remote_get_cached( array(

    'url'     => sfw_create_request_url( 'currencies', $spreadshirt_id ),

    'filter'  => 'currency',

    'expire'  => WEEK_IN_SECONDS,

    'cache'   => $cache,

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));

  // make sure we don't load the wrong data from the options cache.
  if( $cache && !$flush_cache && is_spreadshirt_object( $data ) && $data->id != $spreadshirt_id )
        return sfw_get_currency( $spreadshirt_id, true );



  return $data;
}




/**
 * Retieve currency id
 *
 * @param  int $currency_id A currency id
 * @return int Currency ID. Default is the shops currency id.
 * @since 1.0.0
 */

function sfw_get_currency_id( $currency_id = false ) {

  if( false === $currency_id ) {
    $shop = sfw_get_shop();

    if( $shop )
      $currency_id = $shop->currency->id;
  }

  return $currency_id;
}




/**
 * Retrieve currency decimalCount
 *
 * @param  int $currency_id A currency id
 * @return string
 * @since 1.0.0
 */

function sfw_get_decimal_count( $currency_id = false ) {
  return !empty( $currency = sfw_get_currency( $currency_id ) )
    ? apply_filters('spreapdress/currency/decimal_count', (string) $currency->decimalCount, $currency )
    : _x( '2', 'fallback decimalCount', 'apparelcuts-spreadshirt' );
}




/**
 * Retrieve the shops currency pattern
 *
 * @param  int $currency_id A currency id
 * @return string
 * @since 1.0.0
 */

function sfw_get_currency_pattern( $currency_id = false ) {
  return !empty( $currency = sfw_get_currency( $currency_id ) )
    ? apply_filters('spreapdress/currency/pattern', (string) $currency->pattern, $currency )
    : _x( '% $', 'fallback currency pattern, % = number, $ = currency symbol', 'apparelcuts-spreadshirt' );
}




/**
 * Replaces currency pattern with given parameters
 *
 * @param  string|float $value Value replacement.
 * @param  string $symbol Optional. Symbol replacement
 * @param  int $currency_id A currency id
 *
 * @return string
 *
 * @since 1.0.0
 */

function sfw_use_currency_pattern( $value, $symbol = false, $currency_id = false ) {

  if( empty( $symbol ) )
    $symbol = sfw_get_currency_symbol( $currency_id );

  $retval = sfw_get_currency_pattern( $currency_id );
  $retval = str_replace( '%', $value, $retval );
  $retval = str_replace( '$', $symbol, $retval );

  return $retval;
}




/**
 * Retrieve the shops currency symbol
 *
 * @param  int $currency_id A currency id
 * @return string
 * @since 1.0.0
 */

function sfw_get_currency_symbol( $currency_id = false ) {
  return !empty( $currency = sfw_get_currency( $currency_id ) )
    ? apply_filters('spreapdress/currency/symbol', (string) $currency->symbol, $currency )
    : '';
}




/**
 * Retrieve the shops currency isocode
 *
 * @param  int $currency_id A currency id
 * @return string
 * @since 1.0.0
 */

function sfw_get_currency_isocode( $currency_id = false ) {
  return !empty( $currency = sfw_get_currency( $currency_id ) )
    ? apply_filters('spreapdress/currency/isocode', (string) $currency->isoCode, $currency )
    : '';
}

