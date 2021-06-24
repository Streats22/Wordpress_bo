<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Helper for debugging the Plugin
 *
 * @since 1.0.0
 */



// disable debugging if not previously enabled
sfw_define( 'DEBUG', false );




/**
 * Check if debugging is enabled
 *
 * @return bool
 * @since 1.0.0
 */

function sfw_do_debug() : bool {

  return WP_DEBUG || sfw_constant( 'DEBUG' );
}




/**
 * Trigger an error at runtime.
 *
 * Use sfw_log() for logging general information in debug mode instead.
 *
 * @see trigger_error
 * @param  string|callable $message A message string or function that returns a message string.
 *                                  Wrapping extensive functions (like var_export) in an anonymous
 *                                  function is about 10x faster when debugging is off
 * @param  string $function calling __FUNCTION__ or __METHOD__
 * @param  mixed $type see trigger_error
 * @since  1.0.0
 */

function sfw_debug( $message, $function = '', $type = E_USER_NOTICE ) {

  if( sfw_do_debug() ) {
    if( is_callable( $message ) ) {
      $message = call_user_func( $message );
    }
    trigger_error( 'Spreadshirt for Wordpress: ' . ( $function ? "($function) " : '' ) . $message , $type );
  }

}




/**
 * Trigger Notice at runtime
 *
 * Only when SFW_DEBUG is set to 3.
 *
 * @see trigger_error
 * @param  string|callable $message A message string or function that returns a message string.
 *                                  Wrapping extensive functions (like var_export) in an anonymous
 *                                  function is about 10x faster when debugging is off
 * @param  string $function calling __FUNCTION__ or __METHOD__
 * @since  1.0.0
 */

function sfw_debug_insane( $message, $function = '' ) {

  if( sfw_constant('DEBUG') === 3 ) {
    sfw_debug( $message, $function, E_USER_NOTICE );
  }
}




/**
 * Trigger an error at runtime.
 *
 * Use sfw_log() for logging general information in debug mode instead.
 *
 * @see error_log
 * @param  string|callable $message A message string or function that returns a message string.
 *                                  Wrapping extensive functions (like var_export) in an anonymous
 *                                  function is about 10x faster when debugging is off
 * @param  string $function calling __FUNCTION__ or __METHOD__
 * @since  1.0.0
 */

function sfw_debug_log( $message, $function = '' ) {

  if( sfw_do_debug() ) {
    if( is_callable( $message ) ) {
      $message = call_user_func( $message );
    }
    error_log( 'Spreadshirt for Wordpress: ' . ( $function ? "($function) " : '' ) . $message );
  }
}




/**
 * Log Notice at runtime
 *
 * Only when SFW_DEBUG is set to 3.
 *
 * @see error_log
 * @param  string|callable $message A message string or function that returns a message string.
 *                                  Wrapping extensive functions (like var_export) in an anonymous
 *                                  function is about 10x faster when debugging is off
 * @param  string $function calling __FUNCTION__ or __METHOD__
 * @since  1.0.0
 */

function sfw_debug_log_insane( $message, $function = '' ) {

  if( sfw_constant('DEBUG') === 3 ) {
    sfw_debug_log( $message, $function );
  }
}




/*
 * Ad debug class to admin area
 */

add_filter( 'admin_body_class', function( $classes ) {

  if( sfw_constant( 'DEBUG' ) ) {

    $classes .= ' sfw-debug ';

  }

  return $classes;
});




