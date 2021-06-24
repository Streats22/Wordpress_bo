<?php if ( ! defined( '\ABSPATH' ) ) exit;
/**
 * Helper for API Requests
 *
 * @since 1.0.0
 */




/**
* Retrieve the global Spreadshirt Api object
*
* The Shop must be configured before using this object. In other
* case, you should use your own instance of the WP_Spreadshirt_Api class
*
* @see WP_Spreadshirt_Api
*
* @return WP_Spreadshirt_Api | false
*
* @since 1.0.0
*/

function sfw_api( ) {

  global $sfw_api;


  if( ! $sfw_api instanceof WP_Spreadshirt_Api ) {

    if( !sfw_is_shop_properly_configured() ) {

      // can't create api object when configuration does not exist
      sfw_doing_it_wrong( __FUNCTION__, 'Wrong use of Spreadshirt-API before Spreadshirt for Wordpress was configured.', E_USER_NOTICE );
      return false;
    }

    $sfw_api = new WP_Spreadshirt_Api( sfw_get_api_args() );
  }

  return $sfw_api;

}




/**
 * Retriev a set of Args used to instantiate a WP_Spreadshirt_Api object
 *
 * @return array
 *
 * @since 1.0.0
 */

function sfw_get_api_args() {

  $args = array();

  if( sfw_is_shop_properly_configured() ) {

    $args += array(
      'user-agent' => sfw_api_user_agent(),
      'host'       => sfw_get_host(),
      'apikey'     => sfw_apikey(),
      'secret'     => sfw_secret(),
    );

  }

  /**
   * Args to instantiate the global Api Object
   *
   * @param array $args
   *
   * @since 1.0.0
   */

  $args = apply_filters('sfw/api/args', $args );

  return $args;
}




/**
 * Retrieve the plugins User Agent for api requests
 *
 * @return string User Agent String
 */

function sfw_api_user_agent( ) {

  /**
   * The Plugins User Agent
   *
   * @param string User Agent String
   *
   * @since 1.0.0
   */

  return apply_filters( 'sfw/user_agent', sprintf(
    'spreadshirt-for-wordpress/%s (%s; %s)',
    sfw_version(),
    get_bloginfo( 'wpurl' ),
    get_bloginfo( 'admin_email' )
  ) );

}




/**
 * Adds basic data to all api requests if they do not exist already
 *
 * @since 1.0.0
 */

function sfw_proxy_add_api_args() {

  /**
   * The default args
   *
   * @param array $args
   *
   * @since 1.0.0
   */

  add_filter( 'wp-rest-spreadshirt/api_args', function( $args ){

    $args += sfw_get_api_args();

    return $args;

  });
}




/**
* Performs an Api request
*
* @see wp_safe_remote_request
* @see sfw_api
*
* @param $args
*
* @return WP_Error | $resource
*
* @since 1.0.0
*/

function sfw_remote_request( $args ) {

  // args will be passed to wp_safe_remote_request later
  $args = wp_parse_args( $args, array(

    // method
    'method'        => 'GET',

    // url, can but must not contain the host
    'url'           => '',

    // query args
    'query_args'    => array(),

  ));


  /**
   * Remote request args. Will be passed to wp_safe_remote_request later
   *
   * @param array $args
   */

  $args = apply_filters( 'sfw/remote_request/query_args', $args );


  $url        = $args['url'];
  $query_args = $args['query_args'];
  unset( $args['url'], $args['query_args'] );

  sfw_api()->request( $url, $query_args, $args );
  $json = sfw_api()->get_json_response();

  $remote_request_num = sfw_var('num_remote_requests') ?: 0;
  sfw_var( 'num_remote_requests', $remote_request_num++ );

  return is_wp_error( $json )
    ? sfw_pass_error( $json )
    : apply_filters( 'sfw/remote_response', $json );
}




/**
 * Perform an remote GET request
 *
 * @see sfw_remote_request
 *
 * @param  array $args
 *
 * @return WP_Error|mixed
 */

function sfw_remote_get( $args ) {

  $args['method'] = 'GET';

  return sfw_remote_request( $args );
}




/**
 * Perform an remote POST request
 *
 * @see sfw_remote_request
 *
 * @param  array $args
 *
 * @return WP_Error|mixed
 */

function sfw_remote_post( $args ) {

  $args['method'] = 'POST';

  return sfw_remote_request( $args );
}




/**
 * Create relative request path
 *
 * Joins all given arguments separated by an slash
 *
 * @param string One ore more scalar values
 *
 * @return string
 *
 * @since 1.0.0
 */

function sfw_create_request_url( ) {

  return implode( '/', func_get_args() );
}




/**
 * Create relative request path in the shops namespace
 *
 * @param string One ore more scalar values
 *
 * @return string
 *
 * @since 1.0.0
 */

function sfw_create_shop_request_url( ) {

  return call_user_func_array(
    'sfw_create_request_url',
    array_merge(
      array( 'shops', sfw_get_shop_id() ),
      func_get_args()
    )
  );
}




/**
 * Requests and returns a cached api resource
 *
 * It is recommended to provide a cache object to optimize the performance.
 *
 * @param array  $args
 *
 * @return false|WP_Error|object Returns data on success. Returns WP_Error on
 *                                failure if transmit_errors is set true, else returns
 *                                boolean false
 *
 * @since 1.0.0
 */

function sfw_remote_get_cached( $args ) {


  $args = wp_parse_args( $args, array(
      // request url
      'url'             => null,
      // request query args
      'query_args'      => array( 'fullData' => 'true' ),
      // instance of an cache class
      'cache'           => null,
      // if the cache should be flushed
      'flush'           => false,
      // filter slug
      'filter'          => 'get_resource',
      // when to expire the resource
      'expire'          => WEEK_IN_SECONDS,
      // transmit errors
      'transmit_errors' => false
  ) );

  extract( $args );

  $args = apply_filters( 'sfw/sfw_remote_get_cached/args', $args );

  // filter expire
  $expire = apply_filters( 'sfw/'.$filter.'/expire', $expire );

  // if not set use default cache
  if( is_null( $cache ) ) {

    ksort( $query_args );
    $key = md5( $url . serialize( $query_args ) );

    $cache = new SFW_Remote_Cache_Transient( $key );
  }

  if( sfw_constant('AVOID_CACHE') || !$cache || $flush || $cache->empty() ) {

    // remote get resource
    $response = sfw_remote_get(array(
      'url'        => $url,
      'query_args' => $query_args
    ));

    if( !empty( $response ) && !is_wp_error( $response ) )
      $response = apply_filters( 'sfw/'.$filter.'/response', $response );

    $cache && $cache->set( $response, $expire );

    $resource = $response;
  }

  else {

    $resource  = $cache->get();
  }


  if( empty( $resource ) )
    $resource = sfw_error( 'Empty Resource' );

  if( is_wp_error( $resource ) )
    $resource->add( __FUNCTION__, __FUNCTION__, $args );

  // flatten errors if requested
  if( !$transmit_errors && is_wp_error( $resource ) )
    $resource = false;

  return ( $resource && !is_wp_error( $resource ) )
    ? apply_filters( 'sfw/'.$filter, $resource )
    : sfw_pass_error( $resource );
}




/**
 * Checks for a valid api response casted into an Object
 *
 * Spreadshirt API responses usualy contain an href attributes on root level containing the request url
 *
 * @param object Object
 *
 * @return boolean
 *
 * @since 1.0.0
 */

function is_spreadshirt_object( $object ) : bool {

  $retval = false;

  if( is_object( $object ) ) {

    if( property_exists( $object, 'href' ) ) {

      // not checking against the full host to preserve flexibility
      $hosts = array( 'https://api.spreadshirt' );

      foreach( $hosts as $host ) {

        if( 0 === strpos( $object->href, $host ) ) {
          $retval = true;
          break;
        }

      }

    }

  }


  /**
   * Check if $object is a Spreadshirt API response
   *
   * @param $retval The current evaluation
   * @param $object The Object to check
   *
   * @return bool
   *
   * @since 1.0.0
   */

  return apply_filters( 'is_spreadshirt_object', $retval, $object );
}




/**
 * Retrieves any remote page and caches the result
 *
 * @param  string $url
 * @param  int $expires Time in seconds
 * @param  array $args passed to wp_safe_remote_get
 *
 * @return string remote body
 *
 * @since  1.0.0
 */

function sfw_retrieve_remote_page( $url, $expires = WEEK_IN_SECONDS, $args = array() ) {


  // caching
	$transient_key = md5( $url );

	if ( sfw_constant('AVOID_CACHE') || false === ( $remote_content = get_transient( $transient_key ) ) ) {

		$remote_content = wp_safe_remote_get( $url, $args	);

		if( is_wp_error( $remote_content ) )
      $expires = HOUR_IN_SECONDS / 2;
		else {
  		$remote_content = wp_remote_retrieve_body( $remote_content );

  		$expires  = apply_filters( 'sfw/pages/expires', WEEK_IN_SECONDS, $url, $args );
    }

		set_transient(
			$transient_key,
			$remote_content,
			$expires
		);

	}

  return $remote_content;
}