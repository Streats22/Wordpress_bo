<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
 * @ignore
 * @see _doing_it_wrong
 * @since 1.0.0
 */

function sfw_doing_it_wrong( $function, $message ) {

  return _doing_it_wrong( $function, $message, null );
}



/**
 * Writes a message to the Log
 *
 * @param  string $message A message
 *
 * @since  1.0.0
 */

function sfw_log( $message = 'SFW General Error') {

  if( sfw_do_debug() && defined( 'WP_DEBUG_LOG' ) && constant( 'WP_DEBUG_LOG' ) ) {
    error_log( $message );
  }
}


/**
 * Creates general WP_Error
 *
 * @param string $message An error message
 * @param mixed $data Any data
 * @return WP_Error
 * @since 1.0.0
 */

function sfw_error( $message = '', $data = '' ) {

  return sfw_create_error( 'generic', $message, $data );

}



/**
 * Creates WP_Error
 *
 * @param string $code Errorcode, will be prefixed.
 * @param string $message An error message
 * @param mixed $data Any data
 * @return WP_Error
 * @since 1.0.0
 */

function sfw_create_error( $code = 'generic', $message = '', $data = '' ) {

  if( empty( $message ) ) {
    $message = __( 'An unspecified error has happened.', 'apparelcuts-spreadshirt' );
  }

  $error = new WP_Error( 'sfw-'.$code, $message, $data );


  return sfw_pass_error( $error );

}



/**
 * Adds backtrace information to WP_Error in Debugmode
 *
 * @param  WP_Error If no instance of WP_Error is given, it will create a new one.
 * @return WP_Error
 * @since 1.0.0
 */

function sfw_pass_error( $error ) {

  if( !is_wp_error( $error ) )
    return sfw_error();

  if( sfw_do_debug() )
    $error->add( 'sfw-backtrace', wp_debug_backtrace_summary() );

  //$error->add( 'sfw-debug', 'sfw-debug' );

  return $error;

}



/**
 * Creates specific WP_Error
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_item_error( $el = 'Unknown Item', $id = null ) {

  return sfw_create_error( 'retrieval',
    sprintf(
      _x('Could not retrieve %s.', 'Could not retrieve item like Article, Design etc.', 'apparelcuts-spreadshirt' ),
      is_null( $id ) ? $el : sprintf( __( '%s with ID %s', 'apparelcuts-spreadshirt' ), $el, $id )
    )
  );
}



/**
 * Creates specific WP_Error
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_novalue_error( $el = 'unknown' ) {

  return sfw_create_error( 'retrieval',
    sprintf(
      __('Variable %s can not be empty', 'apparelcuts-spreadshirt' ),
      $el
    )
  );
}



/**
 * Creates specific WP_Error
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_value_error( $data = '' ) {

  return sfw_create_error( 'retrieval', __('Type of variable not accepted', 'apparelcuts-spreadshirt' ), $data );
}

