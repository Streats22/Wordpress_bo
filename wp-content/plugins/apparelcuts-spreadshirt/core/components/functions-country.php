<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Retrieve country data
 *
 * @param  boolean $spreadshirt_id
 * @param  boolean $flush_cache
 * @param  boolean $transmit_errors
 *
 * @return object|false|WP_Error
 *
 * @since 1.0.0
 */

function sfw_get_country( $spreadshirt_id = false, $flush_cache = false, $transmit_errors = false ){

  $spreadshirt_id = sfw_get_country_id( $spreadshirt_id );

  if( empty( $spreadshirt_id ) ) return false;

  $cache = $spreadshirt_id === sfw_get_country_id()
    ? new SFW_Remote_Cache_Option_Autoload( 'country' )
    : null; // default to transient cache

  $data = sfw_remote_get_cached( array(

    'url'     => sfw_create_request_url( 'countries', $spreadshirt_id ),

    'filter'  => 'country',

    'expire'  => WEEK_IN_SECONDS,

    'cache'   => $cache,

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));

  // make sure we don't load the wrong data from the options cache.
  if( $cache && !$flush_cache && is_spreadshirt_object( $data ) && $data->id != $spreadshirt_id )
      return sfw_get_country( $spreadshirt_id, true );

  return $data;
}




/**
 * Retrieve the country id
 *
 * If Id is false, the function will try to guess the Id from the current context.
 * In most cases this will be the shop default country id.
 *
 * @param  string $spreadshirt_id
 *
 * @return int Country ID. Default is the shops country id.
 *
 * @since  1.0.0
 */

function sfw_get_country_id( $spreadshirt_id = false ) {

  $country_id = false === $spreadshirt_id
    ? sfw_get_shop()->country->id
    : $spreadshirt_id;

  return apply_filters( 'sfw/country/id', $country_id, $spreadshirt_id );
}




/**
 * Retrieve the decimalPoint
 *
 * @param  string $spreadshirt_id A country Id.
 * @return string
 *
 * @since 1.0.0
 */

function sfw_get_decimal_point( $spreadshirt_id = false ) {

  return !empty( $country = sfw_get_country( $spreadshirt_id ) )
    ? apply_filters('spreapdress/country/decimal_point', (string) $country->decimalPoint, $country )
    : _x( '.', 'fallback decimal point', 'apparelcuts-spreadshirt' );
}




/**
 * Retrieve the thousandsSeparator
 *
 * @param  string $spreadshirt_id A country Id.
 * @return string
 *
 * @since 1.0.0
 */

function sfw_get_thousands_separator( $spreadshirt_id = false ) {

  return !empty( $country = sfw_get_country( $spreadshirt_id ) )
    ? apply_filters('spreapdress/country/thousands_separator', (string) $country->thousandsSeparator, $country )
    : _x( ',', 'fallback thousandsSeparator', 'apparelcuts-spreadshirt' );
}





/**
 * Retrieve the shops length unit
 *
 * @param  string $spreadshirt_id A country Id.
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_length_unit( $spreadshirt_id = false ) {

  return !empty( $country = sfw_get_country( $spreadshirt_id ) )
    ? apply_filters('spreapdress/country/length/unit', (string) $country->length->unit, $country )
    : false;
}





/**
 * Retrieve the shops length unitFactor
 *
 * @param  string $spreadshirt_id A country Id.
 * @return false|float
 *
 * @since 1.0.0
 */

function sfw_get_length_unit_factor( $spreadshirt_id = false ) {

  return !empty( $country = sfw_get_country( $spreadshirt_id ) )
    ? apply_filters('spreapdress/country/length/unitfactor', (float) $country->length->unitFactor, $country )
    : false;
}





/**
 * Formats a float number to length respecting the spreadshirt shops country settings
 *
 * @param  [type] $origvalue
 * @param  [type] $origunit
 *
 * @return [type]
 *
 * @since 1.0.0
 */

function sfw_length_format( $origvalue, $origunit ) {

	if( $origunit != 'mm') {

    // i have not seen anything other than mm yet
		$newvalue = (float)$origvalue;
		$newunit  = $origunit;
	}
	else {
		$newvalue = (float)$origvalue/10/sfw_get_length_unit_factor();
		$newunit  = sfw_get_length_unit();
	}

	$newvalue = sfw_number_format( $newvalue );
	//var_dump( $origvalue, $origunit,$this->country->length );

	return apply_filters(
		'sfw/length_format',
		array(
			$newvalue,
			$newunit,
			$newvalue.' '.$newunit
		),
		$origvalue,
		$origunit
	);
}




/**
 * [sfw_measure_format description]
 *
 * @param  object $measure
 *
 * @return [type]
 *
 * @since 1.0.0
 */

function sfw_measure_format( $measure ){

	$length = sfw_length_format( $measure->value->value, $measure->value->unit );

	if( $length[1] == 'mm') {
		$length = array(
			sfw_number_format( $length[0]/10, 2),
			'cm',
			sfw_number_format( $length[0]/10, 2).' cm'
		);
	}

	return apply_filters(
		'sfw/measure_format',
		$length
	);
}

