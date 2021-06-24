<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
 * Check if the Shop is configured
 *
 * @return boolean
 */

function sfw_is_shop_properly_configured() {

  return (bool) sfw_get_activated_shop_id();
}




/**
 * get the current Shop ID
 *
 * @return string
 */

function sfw_get_activated_shop_id() {

  return get_option( 'sfw_activated_shop_id' );
}





/**
 * Updates the shop id
 *
 * @ignore
 * @param  string $shop_id
 * @since  1.0.0
 */

function sfw_update_activated_shop_id( $shop_id ) {

  $current_shop_id = sfw_get_activated_shop_id();

  if( $current_shop_id == $shop_id )
    return;

  if( $current_shop_id && !$shop_id && $force_deactivation )
    do_action( 'sfw/deactivate_shop', $current_shop_id );

  if( !$current_shop_id && $shop_id )
    do_action( 'sfw/activate_shop', $current_shop_id );

  if( $shop_id && $current_shop_id )
    do_action( 'sfw/replace_shop', $current_shop_id, $shop_id );


  // refresh cache
  sfw_get_shop( $shop_id, true );


  update_option( 'sfw_activated_shop_id', $shop_id, 'yes' );
}




/**
 * Validates the core shop data
 *
 * Deactivates the shop if the data is broken
 *
 * @since  1.0.0
 */

function sfw_validate_shop_configuration() {

  $shop_id = get_field( 'sfw_shop_id', 'options' );

  if( empty( get_field('sfw_shop_apikey', 'options') ) )
    $shop_id = false;

  if( empty( get_field('sfw_shop_secret', 'options') ) )
    $shop_id = false;

  if( !in_array( sfw_get_host(), array( 'net', 'com' ) ) )
    $shop_id = false;

  $shop_id = apply_filters('sfw/validate_shop_configuration', $shop_id );

  sfw_update_activated_shop_id( $shop_id );

}




/**
 * automatically updates shop meta data that is currenty unavailable via api
 *
 * @since  1.0.0
 */

function sfw_update_noapi_shop_metadata() {

  if( ! sfw_is_shop_properly_configured() )
    return;

  $url = sprintf( 'https://shop.spreadshirt.%s/%s', sfw_get_host(), sfw_get_shop_id() );

  $response = wp_safe_remote_request( $url, array( 'redirection' => 0 ) );

  if( !is_wp_error( $response ) ) {

    $location = wp_remote_retrieve_header( $response, 'Location');
    $matches;


    if( preg_match( '#^https://shop.spreadshirt.([^./]{2,3}(:?\.[^./]{2,3})?)/([^/]+)#i', $location, $matches ) ) {

      update_option( 'sfw_shop_meta_tld', $matches[1],  'yes' );
      update_option( 'sfw_shop_meta_name', $matches[3], 'yes' );
    }
  }

  do_action( 'sfw/update_noapi_shop_metadata', sfw_get_shop_id() );
}




/**
 * Retrieve the host
 *
 * @return string net or com
 * @since  1.0.0
 */

function sfw_get_host() {

 return get_field( 'sfw_shop_host', 'option' );
}




/**
 * Echoes the host
 *
 * @since  1.0.0
 */

function sfw_host() {

  echo sfw_get_host();
}




/**
 * Retrieve the apikey
 *
 * @return string|null
 * @since  1.0.0
 */

function sfw_apikey() {

 return get_field( 'sfw_shop_apikey', 'option' );
}




/**
 * Retrieve the apisecret
 *
 * @return string|null
 * @since  1.0.0
 */

function sfw_secret() {

  return get_field( 'sfw_shop_secret', 'option' );
}




/**
* retrieve the shop name, possibly equal to shop id
*
* @return string
* @since 1.0.0
*/

function sfw_get_shop_name() {

  return get_option( 'sfw_shop_meta_name', sfw_get_shop_id() );
}




/**
* echoes the shop name, possibly equal to shop id
*
* @since 1.0.0
*/

function sfw_shop_name() {

  echo sfw_get_shop_name();
}




/**
* retrieve the shop Top Level Domain e.g. .com
*
* @return string
*
* @since 1.0.0
*/

function sfw_get_shop_tld() {

  return get_option( 'sfw_shop_meta_tld', sfw_get_host() );
}




/**
* Echoes the shop Top Level Domain e.g. .com
*
* @since 1.0.0
*/

function sfw_shop_tld() {

  echo sfw_get_shop_tld();
}




/**
* retrieve the Platform Tld
*
* @return string
*
* @since 1.0.0
*/

function sfw_get_platform_tld() {

  return get_option( 'sfw_shop_meta_platform_tld', sfw_get_host() );
}




/**
* Echoes the Platform Tld
*
* @since 1.0.0
*/

function sfw_platform_tld() {

  echo sfw_get_platform_tld();
}





/**
* Generates the Shop Url
*
* @return string Url
*
* @since 1.0.0
*/

function sfw_get_real_shop_url( ) {


	$url = sprintf( 'https://shop.spreadshirt.%s/%s/',
		sfw_get_shop_tld(),
		sfw_get_shop_name()
	);

	return apply_filters( 'sfw/real_shop_url', $url );

}


/**
* Generates a Url for Spreadshird Shop resources
*
* @param string $extpage_slug
*
* @return string Url
*
* @since 1.0.0
*/

function sfw_get_real_shopdata_url( $extpage_slug ) {


	$url = sfw_get_real_shop_url() .
    sprintf( 'shopData/%s?locale=%s',
  		$extpage_slug,
  		sfw_get_locale()
  	);

	return $url;

}



