<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
 * Retrieve the shops locale
 *
 * @return false|string e.g. de_DE, en_UK
 *
 * @since 1.0.0
 */

function sfw_get_locale(){

  $locale = sprintf( '%s_%s',
    sfw_get_language()->isoCode,
    sfw_get_country()->isoCode
  );

  return apply_filters( 'sfw/locale', $locale );
}




/**
 * Echoes the locale
 *
 * @see sfw_get_locale
 * 
 * @since 1.0.0
 */

function sfw_locale(){

  echo  sfw_get_locale();
}
