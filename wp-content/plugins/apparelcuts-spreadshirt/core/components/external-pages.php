<?php if ( ! defined( '\ABSPATH' ) ) exit;


// define external pages
sfw_var( 'external_pages', array(

	/////// Return policy

	array(
		'slug' => 'return-policy',
    'is_required_page' => false,
    '__callback' => function(){

      return sfw_wrap_external_content(
        sfw_get_remote_service_page( '115000984869' ),
        'return-policy'
      );
    }
	),

	/////// Terms and conditions

	array(
		'slug' => 'partner-terms-and-conditions',
    'is_required_page' => false,
    '__callback' => function(){

      return sfw_wrap_external_content(
        sfw_get_remote_service_page( '115000991325' ),
        'return-policy'
      );
    }
	),

	/////// Shipping costs

	array(
		'slug' => 'shipping-costs',
		'label' => __('Shipping Costs', 'apparelcuts-spreadshirt' ),
		'post_name' => sanitize_key( _x('shipping-costs', 'A page slug', 'apparelcuts-spreadshirt' ) ),
    'is_required_page' => true,
		'instructions' => __('A page displaying shipping costs and details', 'apparelcuts-spreadshirt' ),
    'post_content' => '[sfw shipping-calculator][sfw external="shipping-costs"]',
	  'acf_append'   => function(){
	    $text = sprintf(
	      _x('In most cases, this page should contain the %s and %s shorttags', '%s = shorttag code', 'apparelcuts-spreadshirt' ),
	      sprintf( '<code>%s</code>', '[sfw shipping-calculator]' ),
	      sprintf( '<code>%s</code>', '[sfw external="shipping-costs"]' )
	    );

	    printf( '<p class="sfw-acf-extended-instructions">%s</p>', $text );
	  },
    '__callback' => function(){

      return sfw_wrap_external_content(
        sfw_get_remote_service_page( '115000993925' ),
        'return-policy'
      );
    }
	)

) );




/*
 * register page option for required pages
 */

add_action( 'sfw/init', function(){

	foreach( sfw_var( 'external_pages' ) as $sfw_external_page ) {

    if( $sfw_external_page['is_required_page'] ) {
  		sfw_register_dynamic_page( $sfw_external_page );
    }
  }

}, 9 );




/*
 * add shortcode
 */

sfw_add_shortcode( 'external', function( $atts, $content ){

  $atts = shortcode_atts( array(
		'external' => 'return-policy',
	), $atts );

  return sfw_get_external_page_code( $atts['external'] );

});




/**
 * Check if external page settings exists
 *
 * @param  string  $slug
 * @return boolean
 * @since  1.0.0
 */

function sfw_is_external_page( $slug ) : bool {

  return !!sfw_get_external_page( $slug );
}




/**
 * Retrieve external page settings
 *
 * @param  [type] $slug
 * @return [type]
 * @since  1.0.0
 */

function sfw_get_external_page( $slug ) {

  foreach( sfw_var( 'external_pages' ) as $page ) {
    if( $page['slug'] === $slug )
      return $page;
  }
  return false;
}




/**
 * Retrieve the HTML Code of an external page
 *
 * @param  string $slug
 * @return string
 * @since  1.0.0
 */

function sfw_get_external_page_code( $slug ) {

  // allow inserting page_ids directly
  if( is_numeric( $slug ) ) {

    return sfw_wrap_external_content(
      sfw_get_remote_service_page( $slug )
    );
  }

  $page = sfw_get_external_page( $slug );

  if( !$page )
    return '';

  if( is_callable( $page['__callback'] ) )
    return call_user_func( $page['__callback'] );
}




/**
 * Retrieves a Spreadshirt Help Page
 *
 * @uses wp_kses
 *
 * @param  string $page_id Id of a Spreadshirt Help Page
 * @return WP_Error|string
 * @since  1.0.0
 */

function sfw_get_remote_service_page( $page_id ) {

  $retval = '';

  // modify locale
  $locale = sfw_get_locale();
  $locale = str_replace( '_', '-', strtolower( $locale ) );

  $url = 'https://service.spreadshirt.com/hc/%locale%/articles/'.$page_id;
  $url = str_replace( '%locale%', $locale, $url );

  $body = sfw_retrieve_remote_page( $url );

  if( is_wp_error( $body ) ) {
    sfw_log( __('Could not retrieve Service Page').$page_id );
    return $retval;
  }

  $matches = false;

  // extract article tag
  if( preg_match( '#<article(.*)>(.*)<\/article>#msiU', $body, $matches ) ){

    $retval = $matches[0];

    $retval = preg_replace( '#<script(.*)<\/script>#msiU', '', $retval );
    $retval = preg_replace( '#<style(.*)<\/style>#msiU', '', $retval );

    $retval = wp_kses( $retval , array(
      'h1' => array(),
      'h2' => array(),
      'h3' => array(),
      'h4' => array(),
      'h5' => array(),
      'h6' => array(),
      'p' => array(),
      'strong' => array(),
      'b' => array(),
      'em' => array(),
      'i' => array(),
      'a' => array(
        'href' => array()
      ),
      'br' => array(),
      'header' => array(),
      'footer' => array(),
      'section' => array(
        'class' => array()
      ),
    ) );

    $retval = preg_replace( '#<section class="related-articles">(.*)<\/section>#msiU', '', $retval, 1 );

    $retval = preg_replace( '#<header(.*)<\/header>#msiU', '', $retval, 1 );

    $retval = preg_replace( '#<footer(.*)<\/footer>#msiU', '', $retval, 1 );

  }

  return $retval;

}




/**
 * Creates a div container arround a string
 *
 * @param  string $string
 * @param  string $class
 * @return string
 * @since  1.0.0
 */

function sfw_wrap_external_content( $string, $class = '' ) {

 	return empty( $string )
    ? $string
    : sprintf(
    '<div class="sfw-external-content %s">%s</div>',
    $class ? 'sfw-external-'.$class : '',
    $string
  );
}