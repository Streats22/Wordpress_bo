<?php if ( ! defined( '\ABSPATH' ) ) exit;


/*
* Handle data retrieved from the Spreadshirt API
*
* By default request will fail if Statuscode is greater than 399 and response is
* treaten and parsed as json.
*
*
* Sample Use:
*
*      $args = array(
*       'host' => 'net',
*       'locale' => 'de_DE'
*      );
*
*
*      $api = new WP_Spreadshirt_Api( array() )
*
*/


/**
 * Spreadshirt API Interface
 *
 * @deprecated Will be replaced with a cleaner solution 
 * @since 1.0.0
 */


class WP_Spreadshirt_Api {




  /**
   * The Class Version
   *
   * @var string
   *
   * @since 1.0.0
   */

  static $version = '1.0';




  /**
   * The connection settings
   *
   * @var array
   *
   * @since 1.0.0
   */

  private $settings = array(

    'locale' => '',
    'host'   => '',

    'apikey' => '',
    'secret' => '',

    'user-agent' => null,

    'marketplace' => array(
      'net' => '205909',
      'com' => '117728'
    ),
  );




  /**
  * Parse settings on initiation
  *
  * @see $this::set_settings()
  * @see $this::$settings
  *
  * @param array $args
  *
  * @since 1.0.0
  */

  function __construct( ) {

    $this->set_settings( func_get_arg( 0 ) );
  }




  /**
  * Reparse the settings
  *
  * @param array $args
  *
  * @since 1.0.0
  */

  function set_settings( $args = array() )  {

    $this->settings = wp_parse_args( $args, $this->settings );
  }

  function set_setting( $key, $value )  {

    $this->set_settings( array( $key => $value ) );
  }




  /**
   * Retrieve the Spreadshirt API Server url
   *
   * @return string
   *
   * @since  1.0.0
   */

  public function get_server_url( ) {

    return sprintf(
      '%s://api.spreadshirt.%s/api/v1/',
      $this->get_arg( 'ssl' ) ? 'https' : 'http',
      $this->settings['host']
    );
  }




  /**
   * Retrieve the Spreadshirt API Image Server url
   *
   * @return string
   *
   * @since  1.0.0
   */

  public function get_imageserver_url( ) {

    return sprintf(
      '%s://image.spreadshirtmedia.%s/image-server/v1/',
      $this->get_arg( 'ssl' ) ? 'https' : 'http',
      $this->settings['host']
    );
  }


  /**
   * Stores the current query parameters that will be attached to the query url
   *
   * Will be reset after each query
   *
   * @var array
   */

  public $current_query_params = array();




  /**
   * Set an query paramater
   *
   * @param string $data_key The parameter name
   * @param mixed  $data_value The parameters value
   * @param boolean $unique If existing parameters should be override or
   *                        if the values should be treated as array
   * @since 1.0.0
   */

  function set( $data_key, $data_value, $unique = true ) {

    // fix for bool values
    if( is_bool( $data_value ) )
      $data_value = $this->bool_to_str( $data_value );

    if( $unique ) {

      $this->current_query_params[ $data_key ] = $data_value;
    }
    else {

      if( isset( $this->current_query_params[ $data_key ] ) ) {

        if( is_array( $this->current_query_params[ $data_key ] ) ) {

          $this->current_query_params[ $data_key ][] = $data_value;
        }
        else {

          $this->current_query_params[ $data_key ] = array( $this->current_query_params[ $data_key ], $data_value );
        }
      }
      else {

        $this->current_query_params[ $data_key ] = array( $data_value );
      }
    }
  }




  /**
  * Reset current query params
  *
  * @param $key - Paramenter
  * @return -
  * @since 1.0.0
  */

  function reset_query_params( ) {

    $this->current_query_params = array();
  }




  /**
  * Retrieve
  *
  * @param  (array) $args
  * @return (array) $args
  * @since  1.0.0
  */

  public $last_rendered_query_data = array();

  public function get_query_data( $custom_query_data = array() ) {


    $defaults = array(
      //'fullData' => 'true',
      'mediaType' => 'json'
    );

    if( !empty( $this->settings['locale'] ) )
      $defaults['locale'] = $this->settings['locale'];

    $query_data = wp_parse_args( $this->current_query_params, $defaults );
    $query_data = wp_parse_args( $custom_query_data, $query_data );


    $this->last_rendered_query_data = apply_filters('spreadshirt-api/get_query_data', $query_data );


    return $this->last_rendered_query_data;

  }





  /**
  * Appends Query Data to Url.
  * Prepends Url with Server Url if it doesn't start with a Protokol
  *
  * @param (string) $url
  * @param (array) $custom_query_data
  * @since 1.0.0
  */

  public $last_rendered_url;

  function prepare_url( $_url = '', $custom_query_data = array() ) {


    $url = $_url;

    // relative path
    // autofill the host url if the given url is relative
    // we must know which host
    if( !empty( $this->settings['host'] ) && ! parse_url( $url, PHP_URL_HOST ) ) {


      // remove leading slash
      if( substr( $url, 0, 1 ) === '/' )
        $url = substr( $url, 1 );


      $url = $this->get_server_url( ) . $url;

    }

    $url = add_query_arg( $this->get_query_data( $custom_query_data ), $url );


    $this->last_rendered_url = apply_filters( 'spreadshirt-api/prepare_url', $url, $_url, $custom_query_data );


    return $this->last_rendered_url ;

  }







  /**
  * return a String representation of a boolean
  *
  * @param  (mixed) $value
  * @return (string) 'true' | 'false'
  * @since  1.0.0
  */

  function bool_to_str( $value ) {

    return $value
      ? 'true'
      : 'false';
  }









  /**
  * Save the bare result
  *
  * @since 1.0.0
  */

  public $response;

  private function update_response( $response ) {


    $this->response = apply_filters( 'spreadshirt-api/response', $response );


    // reset data
    $this->reset_query_params();


    return $this->response;

  }






  /**
  * Save the current request args
  *
  * @since 1.0.0
  */

  public $request_args = array();

  private $default_request_args = array(

    //'timeout'     => 5,
    //'redirection' => 5,
    //'httpversion' => '1.0',
    //'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
    //'blocking'    => true,
    //'headers'     => array(),
    //'cookies'     => array(),
    //'body'        => null,
    //'compress'    => false,
    //'decompress'  => true,
    //'sslverify'   => true,
    //'stream'      => false,
    //'filename'    => null

    // method
    'method' => 'GET',

    // if the resource requires an api secret
    'send_secret' => false,

    // if the resource requires an user session
    'session' => false,

    // if the resource is available via https
    'ssl'    => true,

    // headers
    'headers' => array()
  );

  private function prepare_request_args( $args, $defaults = array() ) {

    // merge args
    $defaults = wp_parse_args( $defaults, $this->default_request_args );
    $this->request_args = wp_parse_args( $args, $defaults );

    // add Spread Auth Header to Requests that require an APIKEY
    $this->maybe_add_sprdauth_header();
    $this->maybe_add_user_agent();

    do_action( 'spreadshirt-api/prepare_request_args', $this );

  }

  public function get_arg( $key ) {

    return isset( $this->request_args[ $key ] )
      ? $this->request_args[ $key ]
      : false;
  }


  /**
  * Create a Authorization Header if possible
  *
  * @return (string) HEADER
  * @since 1.0.0
  */

	private function maybe_add_sprdauth_header( ) {

    // bail if exists
    if( isset( $this->request_args['headers']['Authorization'] ) )
      return;

    // bail if no apikey exists
    if( empty( $this->settings['apikey'] ) )
      return;

    // stop if complex Authorization is not required
    if( $this->request_args['send_secret'] )
      $this->request_args['headers']['Authorization'] = $this->get_auth_header();
    else
      $this->request_args['headers']['Authorization'] = $this->get_simple_auth_header();

	}


  public function get_simple_auth_header() {
    return sprintf( 'SprdAuth apiKey="%s"', $this->settings['apikey'] );
  }

  public function get_auth_header() {
    // create signature
    $url       = $this->last_rendered_url;
    $time      = time()*1000;
    $method    = $this->request_args['method'];
    $secret    = $this->settings['secret'];
    $apikey    = $this->settings['apikey'];
    $data      = "$method $url $time";
    $signature = sha1( "$data $secret" );

    return 'SprdAuth apiKey="'.$apikey.'", data="'.$data.'", sig="'.$signature.'"';
  }


  /**
  * Create a User Agent
  *
  * @return (string) HEADER
  * @since 1.0.0
  */

	private function maybe_add_user_agent( ) {

    // bail if exists
    if( isset( $this->request_args['user-agent'] ) )
      return;

    // bail if no apikey exists
    if( !empty( $this->settings['user-agent'] ) ) {
      $this->request_args['user-agent'] = $this->settings['user-agent'];
      return;
    }

    $this->request_args['user-agent'] = self::get_default_user_agent();
    return;

	}


  static function get_default_user_agent() {
    return sprintf(
      'wp-spreadshirt-api/%s (%s; %s)',
      self::$version,
      get_bloginfo( 'wpurl' ),
      get_bloginfo( 'admin_email' )
    );
  }







  /**
  * Returns the parsed response. Other than wp_http functions it will return WP_Error
  * if the statuscode indicates an Error
  *
  * Notice: use $instance->response to retrieve the untouched response
  *
  * @todo parse Spreadshirt Errors into WP_Error
  * @since 1.0.0
  */


  public function get_response( ) {

    $response = $this->response;

    if( is_wp_error( $response ) ) {

      return $response;
    }
    elseif( !is_array( $response ) ) {

      return new WP_Error( 'spreadshirt-api', __('You probably forgot to run a request before requesting the response.') );
    }
    elseif( wp_remote_retrieve_response_code( $response ) >= 400 ) {

      return new WP_Error( 'spreadshirt-api', wp_remote_retrieve_response_message( $response ), array(
        'status_code' => wp_remote_retrieve_response_code( $response ),
        'url' => $this->last_rendered_url
      ) );
    }

    return $response;
  }



  function get_json_response() {

    $response = $this->get_response( );

    if( is_wp_error( $response ) )
      return $response;


    $json         = json_decode( wp_remote_retrieve_body( $response ) );
    $_json_error  = json_last_error();

    if( $_json_error !== JSON_ERROR_NONE ) {

      return new WP_Error( 'spreadshirt-api', __('Failed to parse Response as JSON'), array(
        'json_error' => $_json_error,
        'url' => $this->last_rendered_url
      ) );
    }

    return $json;

  }



  /**
  * Remote Get a Resource
  *
  * @param (string) $url - the URL to Get, can contain $query_data
  * @param (array) $query_data - key/value pairs
  * @param (array) $request_args - Args passed to wp_remote_request
  *
  * @return WP_Error | $response
  * @uses wp_remote_get
  * @since 1.0.0
  */

  public function request( $url, $query_data = array(), $request_args = array() ) {


    $this->prepare_request_args( $request_args );


    $url = $this->prepare_url( $url, $query_data );


    $response = $this->update_response( wp_safe_remote_request( $this->last_rendered_url, $this->request_args ) );



    return $this->get_response( );

  }






  /**
  *
  *
  * @param
  * @return
  * @uses
  * @see
  * @todo
  * @since 1.0.0
  */


  public function get( $url, $query_data = array(), $request_args = array() ) {


    $request_args = wp_parse_args( $request_args, array( 'method' => 'GET' ) );


    return $this->request( $url, $query_data, $request_args );
  }




  /**
  *
  *
  * @param
  * @return
  * @uses
  * @see
  * @todo
  * @since 1.0.0
  */

  public function post( $url, $query_data = array(), $request_args = array() ) {


    $request_args = wp_parse_args( $request_args, array( 'method' => 'POST' ) );


    return $this->request( $url, $query_data, $request_args );
  }






  /**
  *
  *
  * @param
  * @return
  * @uses
  * @see
  * @todo
  * @since 1.0.0
  */

  public function delete( $url, $query_data = array(), $request_args = array() ) {


    $request_args = wp_parse_args( $request_args, array( 'method' => 'DELETE' ) );


    return $this->request( $url, $query_data, $request_args );
  }



}




