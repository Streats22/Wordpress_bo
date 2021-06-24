<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Proxy for the Spreadshirt API
 *
 * @since 1.0.0
 */



if( !class_exists( 'WP_Rest_Spreadshirt_Api' ) ) {

/**
 * Makes the Spreadshirt API available via WP Rest
 *
 * @since 1.0.0
 */

class WP_Rest_Spreadshirt_Api {




  /**
   * namespace for the api route
   *
   * @var string
   *
   * @since 1.0.0
   */

  var $namespace = 'spreadshirt-api/v1';




  /**
   * Initiate
   *
   * @since 1.0.0
   */

  function init() {

     $this->register_rest_route();
  }




  /**
   * Registers the rest route
   *
   * @since  1.0.0
   */

  function register_rest_route() {

    //The Following registers an api route with multiple parameters.
    add_action( 'rest_api_init', function (){

      $args = array(

        'methods' => array( 'GET', 'POST', 'PUT', 'DELETE' ),

        'args' => array(

          'url' => array(

            'required' => true,

            'validate_callback' => array( $this, 'validate_spreadshirt_url' )

          ),

        ),

        'callback' => array( $this, 'parse_request' ),

        'permission_callback' => array( $this, 'permission_callback' )

      );

      register_rest_route( $this->namespace, '/proxy', $args );

    });

  }




  /**
   * Check if value is a url
   *
   * @param  string url of a Spreadshirt API resource
   *
   * @return WP_Error|true
   *
   * @since 1.0.0
   */

  function validate_spreadshirt_url( $value ) {

    if( !is_string( $value ) )
      return new WP_Error( 'rest-error', __('Url is invalid') );

    $parts = parse_url( $value );

    if( !is_array( $parts ) )
      return new WP_Error( 'rest-error', __('Url is invalid') );

    if( 'https' !== $parts['scheme'] )
      return new WP_Error( 'rest-error', __('Please use https as protocol') );

    if( !in_array( $parts['host'], array( 'api.spreadshirt.net', 'api.spreadshirt.com' ) ) )
      return new WP_Error( 'rest-error', __('Host is not allowed') );

    return true;
  }




  /**
   * Get Endpoint without Ids
   *
   * @param  string url of a Spreadshirt API resource
   *
   * @return WP_Error|true
   *
   * @since 1.0.0
   */

  function get_spreadshirt_endpoint( $url ) {

    $parts = parse_url( $url );

    $path = preg_replace( '#(^\/?api\/v[0-9]+\/)|([0-9]+\/)|(\/[0-9]+$)#i', "", $parts['path'] );
    //$path = preg_replace( '##i', "", $path );

    return $path;
  }




  /**
   * Checks if the current user is allowed to perform the request
   *
   * @param object WP_Rest_Request
   *
   * @return WP_Error|true
   *
   * @since 1.0.0
   */

  function permission_callback( $request ) {

    $route = parse_url( $request->get_param('url'), PHP_URL_PATH );

    if( !apply_filters( 'wp-rest-spreadshirt/route_permission', true, $route, $request ) )
      return new WP_Error( 'rest-error', __('You are not allowed to perform this request.') );

    return true;
  }




  /**
   * Strips forbidden params from the request
   *
   * @param  array $params The request params
   * @param  string $method  the request method
   * @param  object $request WP_Rest_Request
   *
   * @return array Filtered array of params
   *
   * @since  1.0.0
   */

  function filter_params( $params, $method, $request ){

    $remove = apply_filters('wp-rest-spreadshirt/forbidden_params', array(
      'sig', 'apikey', 'secret', 'signature', 'method', 'wpnonce', '_wpnonce', 'url', 'send_secret'
    ), $method, $request );

    foreach( $remove as $key ) {
      if( isset( $params[ $key ] ) )
        unset( $params[ $key ] );
    }

    return $params;
  }




  /**
   * Parse the current request
   *
   * @param  object $request WP_Rest_Request
   *
   * @return WP_Error|nothing
   *
   * @since 1.0.0
   */

  function parse_request( $request ) {


    // setup url
    $url = $request->get_param( 'url' );

    $endpoint = $this->get_spreadshirt_endpoint( $url );

    $query_params = $this->filter_params( $request->get_query_params(), 'GET', $request );


    // setup request args
    $wp_request_args = array(
      'method' => $request->get_method(),
      'body' => $request->get_body(),
      'headers' => array(),
    );

    $allowed_headers = apply_filters( 'wp-rest-spreadshirt/forward_headers', array(
      'Content-Type'
    ) );

    foreach( $allowed_headers as $header ) {
      $val = $request->get_header( $header );
      if( !is_null( $val ) )
        $wp_request_args['headers'][$header] = $val;
    }


    // filter request args
    $wp_request_args = apply_filters( 'wp-rest-spreadshirt/request_args', $wp_request_args, $url, $endpoint, $query_params );




    $api_args = apply_filters( 'wp-rest-spreadshirt/api_args', array( ), $url, $endpoint );


    $api      = new WP_Spreadshirt_Api( $api_args );

    // pre request log
    sfw_debug_log_insane( function() use ( $url, $query_params, $wp_request_args ) {
        return "\n" . 'Proxy: ' .$url .
        "\n" . var_export( $query_params, true ).
        "\n" . var_export( $wp_request_args, true );
      },
      __METHOD__
    );

    // request
    $response = $api->request( $url, $query_params, $wp_request_args );

    // response log
    if( wp_remote_retrieve_response_code( $response ) > 400 )
      sfw_debug_log_insane( function() use ( $response ) {
          return "\n" . 'Response: ' .
          "\n" . wp_remote_retrieve_body( $response );
        },
        __METHOD__
      );

    if( is_wp_error( $response ) )
      return $response;


    // headers to proxy backwards
    $proxy_headers = array( 'Content-Type' );
    $proxy_headers = apply_filters( 'wp-rest-spreadshirt/proxy_headers', $proxy_headers, $request, $endpoint  );


    foreach( $proxy_headers as $header ) {
      $_val = wp_remote_retrieve_header( $response, strtolower( $header ) );

      if(!empty( $_val ) ){
        header( sprintf(
          '%s: %s',
          $header,
          $_val
        ), true );

      }
    }

    http_response_code( wp_remote_retrieve_response_code( $response ) );

    echo wp_remote_retrieve_body( $response );

    die();
  }


} // - end class WP_Rest_Spreadshirt_Api


// init the rest api
$wp_rest_spreadshirt_api = new WP_Rest_Spreadshirt_Api();
$wp_rest_spreadshirt_api->init();


} //- end class_exists




/**
 * Retrieve the proxy url
 *
 * @return string
 * @since  1.0.0
 */

function sfw_get_proxy_url() {
  return rest_url( 'spreadshirt-api/v1/proxy' );
}



/**
 * Converts given url to proxied url
 *
 * @param  string $url
 * @return string
 * @since  1.0.0
 */

function sfw_proxy_url( $url ) {

  $proxy = sfw_get_proxy_url();

  return add_query_arg( 'url', urlencode( $url ), $proxy );
}