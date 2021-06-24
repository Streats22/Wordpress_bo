<?php if ( ! defined( '\ABSPATH' ) ) exit;





/**
 * Retrieve the current article price
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 * @param  boolean $flush_cache
 * @param  boolean $transmit_errors
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_article_price_object( $article_selector = false, $flush_cache = false, $transmit_errors = false ){

  // get id
  $article = sfw_get_article( $article_selector );

  if( !$article )
    return false;


  return sfw_remote_get_cached( array(

    'url'     => sfw_create_shop_request_url( 'articles', $article->id, 'price' ),

    'filter'  => 'article/price',

    'cache'   => SFW_Remote_Cache_Entity::get_instance( 'article', $article->id, 'price' ),

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));
}




/**
 * Retrieve an well formatted article price
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 * @return string
 *
 * @since 1.0.0
 */

function sfw_get_article_price( $article_selector = false ) {

  $priceobj = sfw_get_article_price_object( $article_selector );
  $price    = sfw_format_price( $priceobj );

  return apply_filters( 'sfw/article/price', $price, $priceobj );
}





/**
 * Echoes article price
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_article_price( $article_selector = false ) {

  $priceobj = sfw_get_article_price_object( $article_selector );

  echo sfw_get_price_tag( $priceobj );
}






function _sfw_hook_refresh_article_sort_price( $maybe_post, $spreadshirt_id, $entity ) {

  if( sfw_is_wp_post( $maybe_post ) ) {
    if( $price = sfw_get_article_price_object( $maybe_post ) ) {
      sfw_update_object_metadata( $maybe_post, '_orderby_price', intval( $price->vatIncluded * 100 )  );
    }
  }

  return $maybe_post;
}


add_filter( 'sfw/create/article', '_sfw_hook_refresh_article_sort_price', 9, 3 );
add_filter( 'sfw/update/article', '_sfw_hook_refresh_article_sort_price', 9, 3 );