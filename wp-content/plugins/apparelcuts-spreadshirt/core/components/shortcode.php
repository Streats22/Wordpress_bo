<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Allows filtering the [sfw] shorttag by attribute
 *
 * This is done to not flood the shortcode namespace with different codes. Also
 * we don't leave tons of different shortcodes appearing on the page if some
 * functionality is removed in the future.
 *
 * @param  array $atts
 *
 * @return string
 *
 * @since  1.0.0
 */

function _sfw_hook_shortcode( $atts, $content ) {

  $retval = '';

  foreach( $atts as $key => $att ) {

    // allow empty attributes
    $filter = is_numeric( $key ) ? $att : $key;

    $retval = apply_filters( 'sfw/shortcode/'.$filter, $retval, $atts, $content );
  }

	return $retval;
}

add_shortcode( 'sfw', '_sfw_hook_shortcode' );




/**
 * Add a sub-shortcode like [sfw subname]
 *
 * @param string $shortcode
 * @param callable $callback
 * @since 1.0.0
 */


function sfw_add_shortcode( $shortcode, $callback ) {

  add_filter( 'sfw/shortcode/'.$shortcode, function( $retval, $atts, $content ) use ( $callback ) {

    // only allow one shortcode at a time
    if( empty( $retval ) && is_callable( $callback ) ) {
      $retval = call_user_func( $callback, $atts, $content );
    }

    return $retval;

  }, 10, 3 );
}