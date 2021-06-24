<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
 * force leading slash
 *
 * @param  string $string
 * @return string
 * @since 1.0.0
 */

function sfw_leadingslashit( $string ) : string {
  return '/'.ltrim( $string, '/' );
}




/**
 * Sanitizes a Spreadshirt Id
 *
 * @param  string $string
 * @return string
 * @since 1.0.0
 */

function sanitize_spreadshirt_id( $string ){

  $raw = $string;
	$string = preg_replace( '/[^A-Za-z0-9_\-]/', '', $string );

  return apply_filters( 'sanitize_spreadshirt_id', $string, $raw );
}