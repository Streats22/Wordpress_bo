<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Helpers for the SFW WP Rest API
 *
 * @since  1.0.0
 */




/**
 * Retrieve the SFW API namespace
 *
 * @return string
 *
 * @since  1.0.0
 */

function sfw_rest_namespace() {

  return 'sfw/v1';
}




/**
 * Retrieve the SFW API namespaced url
 *
 * @return string url
 *
 * @since  1.0.0
 */

function sfw_rest_url() {

  return rest_url( sfw_rest_namespace() );
}




/**
 * Check if the API Request is authorized
 *
 * @return WP_Error|bool
 *
 * @since  1.0.0
 */

function sfw_rest_default_permission_callback() {

  $allowed =  apply_filters( 'sfw/api/permission_default', sfw_current_user_can_manage_sfw() );

  return $allowed ?: new WP_Error(
    'sfw-rest-permission',
    __("You do not have the permission to access this route.")
  );
}




/**
 * Wrapper for registering a new rest route in der sfw namespace
 *
 * @see register_rest_route
 *
 * @param  string $route
 * @param  array  $args
 * @param  bool $override
 * @param int $priority The priority when the route should be registered at rest_api_init
 *
 * @since  1.0.0
 */

function sfw_register_rest_route( $route = '', $args = array(), $override = false, $priority = 15 ) {

  $args = wp_parse_args( $args, array(
    'permission_callback' => 'sfw_rest_default_permission_callback'
  ) );

  add_action( 'rest_api_init', function() use ( $route, $args, $override ){

    register_rest_route(

      sfw_rest_namespace(),

      sfw_leadingslashit( $route ),

      $args,

      $override

    );

  }, $priority );

}




/**
 * Check if string to itself after sanitization
 *
 * @param  [type] $value
 * @return [type]
 */

function sfw_rest_validate_simple_string( $value ) {

  return $value === sanitize_key( $value );
}




/**
 * Create a rest response
 *
 * @see WP_REST_Response
 *
 * @param  string  $message Any message describing what was done
 * @param  integer $status  A status indicating a succesful request
 *
 * @return WP_REST_Response
 *
 * @since  1.0.0
 */

function sfw_rest_success( $message = '', $status = 200 ) {

  return new WP_REST_Response( array(
    'message' => $message,
    'success' => true
  ), $status );
}