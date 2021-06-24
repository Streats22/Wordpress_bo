<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
 * Retrieve the current Product Object from the global Sfw Object
 *
 * @param  mixed $product_selector See sfw_get_product_id
 * @param  boolean $flush_cache
 * @param  boolean $transmit_errors
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_product( $product_selector = false, $flush_cache = false, $transmit_errors = false ){

  // get id
  $spreadshirt_id = sfw_get_product_id( $product_selector );

  if( empty( $spreadshirt_id ) ) return false;

  return sfw_remote_get_cached( array(

    'url'     => sfw_create_shop_request_url( 'products', $spreadshirt_id ),

    'query_args' => array(
      'fullData' => 'true',
      'locale'   => sfw_get_locale(),
    ),

    'filter'  => 'product',

    'cache'   => SFW_Remote_Cache_Entity::get_instance( 'article', $spreadshirt_id, 'product' ),

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));
}




/**
 * Retrieve the ID of the current Articles Product in the Sfw Loop
 *
 * @param  mixed $product_selector Can be spreadshirt product id, WP_Post, WP_Term
 * @return false|string
 * @since 1.0.0
 */

function sfw_get_product_id( $product_selector = false ) {

  $product_id = false;

  // already the id
  if( maybe_is_spreadshirt_id( $product_selector ) ) {
    $product_id = $product_selector;
  }
  // guess the id by context
  else {

    $article = sfw_get_article( $product_selector );

    if( is_spreadshirt_object( $article ) ) {
      $product_id = @$article->product->id;
    }
  }

  return apply_filters( 'sfw_get_product_id', $product_id, $product_selector );
}




/**
 * Echoes the product id
 *
 * @param  mixed $product_selector See sfw_get_product_id
 * @since 1.0.0
 */

function sfw_product_id( $product_selector = false ) {

  echo sfw_get_product_id( $product_selector );
}
