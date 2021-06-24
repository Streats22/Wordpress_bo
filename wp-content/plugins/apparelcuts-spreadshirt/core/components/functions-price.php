<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Retrieve the vat hint
 *
 * @return string
 * @since  1.0.0
 */

function sfw_get_vat_hint() {

  if( 'net' === sfw_get_host() ) {
    $hint = _x( 'incl. VAT (EU)', '.net vat hint', 'apparelcuts-spreadshirt' );
  }
  else {
    $hint = _x( 'incl. VAT', '.com vat hint', 'apparelcuts-spreadshirt' );
  }

  return apply_filters( 'sfw/vat_hint', $hint );
}




/**
 * Retrieve the vat hint span tag
 *
 * @return string
 * @since  1.0.0
 */

function sfw_get_vat_hint_tag() {

  return sprintf( '<span class="sfw-vat-hint">%s</span>', sfw_get_vat_hint() );
}




/**
 * Retrieve the shipping hint
 *
 * @return string
 * @since  1.0.0
 */

function sfw_get_shipping_costs_hint() {

  $hint = _x( 'plus shipping', 'shipping costs hint', 'apparelcuts-spreadshirt' );

  return apply_filters( 'sfw/shipping_hint', $hint );
}




/**
 * Retrieve the shipping costs page url
 *
 * @return [type]
 * @since  1.0.0
 */

function sfw_get_shipping_costs_url() {

  return sfw_get_page_link( 'shipping-costs' );
}




/**
 * Retrieve the shipping hint linked to the shipping page
 *
 * @return string
 * @since  1.0.0
 */

function sfw_get_shipping_costs_hint_link() {

  return sprintf(
    '<a href="%1$s" target="_blank" class="sfw-shipping-hint">%2$s</a>',
    sfw_get_shipping_costs_url(),
    sfw_get_shipping_costs_hint()
  );
}




/**
 * Retrieve the price hint
 *
 * @return string like incl. Vat, plus shipping
 * @since  1.0.0
 */

function sfw_get_price_hint() {

  $show_vat = get_field( 'sfw-price-hint-includes-vat', 'option' );

  if( $show_vat ) {
    $hint = sprintf( '%s, %s',
      sfw_get_vat_hint_tag(),
      sfw_get_shipping_costs_hint_link()
    );
  }
  else {
    $hint = sfw_get_shipping_costs_hint_link();
  }

  $hint = sprintf( '<span class="sfw-price-hint">%s</span>', $hint );

  return apply_filters( 'sfw/price_hint', $hint );
}






/**
 * Echoes the price hint
 *
 * @return [type]
 * @since  1.0.0
 */

function sfw_price_hint() {

  echo sfw_get_price_hint();
}





/**
 * Format price respecting its currency settings
 *
 * @param  object $priceobj A spreadshirt price object
 *
 * @return string formatted price
 *
 * @since 1.0.0
 */

function sfw_format_price( $priceobj ) {

  $price = sfw_use_currency_pattern(
    sfw_number_format( $price->vatIncluded ),
    false,
    $price->currency->id
  );

  return apply_filters('sfw/format_price', $price, $number );
}





/**
 * Retrieve price markup
 *
 * @param  object $priceobj
 * @return string
 * @since  1.0.0
 */

function sfw_get_price_tag( $priceobj ) {

  $currency_id = $priceobj->currency->id ?: sfw_get_currency_id();

  $value_html    = sprintf(
    '<span itemprop="price" content="%s">%s</span>',
    $priceobj->vatIncluded,
    sfw_number_format( $priceobj->vatIncluded )
  );

  $currency_html = sprintf(
    '<span itemprop="priceCurrency" content="%s">%s</span>',
    sfw_get_currency_isocode( $currency_id ),
    sfw_get_currency_symbol( $currency_id )
  );

  $html =
    '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">'.
      sfw_use_currency_pattern( $value_html, $currency_html, $currency_id ).
      '<link itemprop="availability" href="http://schema.org/InStock" />'.
    '</span>';

	return apply_filters( 'sfw/shemaorg/price_offer', $html, $priceobj );
}




/**
 * Returns $number with as less digits after the decimal point as possible
 *
 * @param  float $number
 *
 * @return float $number
 *
 * @since 1.0.0
 */

function sfw_vat_format( $number ) {

  if( intval( $number ) == $number ) {

    return sfw_number_format( $number, 0 );
  }
  elseif( intval( $number*10 ) == $number*10 ) {

    return sfw_number_format( $number, 1 );
  }
  else {

    return sfw_number_format( $number );
  }
}





/**
 * Formats a float number, respecting the spreadshirt shops country and currency settings
 *
 * @param  float  $number
 * @param  boolean $custom_decimal_count
 * @param  string $country_id A country Id.
 * @param  string $currency_id A currency Id.
 *
 * @return string
 *
 * @since 1.0.0
 */

function sfw_number_format( $number, $custom_decimal_count = false, $country_id = false, $currency_id = false ) {

	if( false === $custom_decimal_count )
    $custom_decimal_count = sfw_get_decimal_count( $currency_id );

	return apply_filters(
		'sfw/number_format',
		number_format(
			(float) $number,
			$custom_decimal_count,
			sfw_get_decimal_point( $country_id ),
			sfw_get_thousands_separator( $country_id )
		),
		$number,
		$custom_decimal_count
	);
}

