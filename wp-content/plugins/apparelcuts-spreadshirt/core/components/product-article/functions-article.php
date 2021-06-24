<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
* This File deals mainly with the JSON response for the article resource
* and Posts with a post_type of 'sfw-product'
*
* @since 1.0.0
*/



/**
 * Checks if the current post is an Article
 *
 * @return boolean
 *
 * @since  1.0.0
 */

function sfw_is_article() {

  return is_singular( 'sfw-product' ) || get_post_type() === 'sfw-product';
}




/**
 * Retrieve the current Article
 *
 * Will return the Article if it exists in the Shop
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 * @param  boolean $flush_cache
 * @param  boolean $transmit_errors
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_article( $article_selector = false, $flush_cache = false, $transmit_errors = false ){

  // get id
  $spreadshirt_id = sfw_get_article_id( $article_selector );

  if( !maybe_is_spreadshirt_id( $spreadshirt_id ) )
    return false;


  return sfw_remote_get_cached( array(

    'url'     => sfw_create_shop_request_url( 'articles', $spreadshirt_id ),
    
    'query_args' => array(
      'fullData' => 'true',
      'locale' => sfw_get_locale(),
    ),

    'filter'  => 'article',

    'cache'   => SFW_Remote_Cache_Entity::get_instance( 'article', $spreadshirt_id ),

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));
}




/**
 * Retrieve article id
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_article_id( $article_selector = false ) {

  return sfw_maybe_guess_entity_spreadshirt_id( 'article', $article_selector );
}




/**
 * Echoes the article id
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_article_id( $article_selector = false ) {

  echo sfw_get_article_id( $article_selector );
}




/**
 * Retrieve a Wordpress Post by Spreadshirt Article Id
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @return WP_Post|false
 *
 * @since 1.0.0 *
 */

function sfw_get_article_post( $article_selector ) {

  if( empty( $spreadshirt_id = sfw_get_article_id( $article_selector ) ) )
    return false;

  return sfw_get_post( 'article', $spreadshirt_id );
}




/**
 * Retrieve the article title
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_article_name( $article_selector = false ) {

  $article_post = sfw_get_article_post( $article_selector );

  return get_the_title( $article_post );
}




/**
 * Echoes the article title
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_article_name( $article_selector = false ) {

  echo sfw_article_name( $article_selector );
}





/**
 * Filters the article title
 *
 * @ignore
 * @since 1.0.0
 */

function _sfw_hook_article_the_title( $title, $post_id  ) {

  if( !$post_id )
    return $title;

  $post = get_post( $post_id );

  if( !sfw_is_wp_post( $post ) )
    return $title;

  if( $post->post_type != 'sfw-product' )
    return $title;

  return sfw_article_has_custom_title( $post )
    ? $title
    : sfw_get_article_name_fallback( $post );
}

add_filter( 'the_title', '_sfw_hook_article_the_title', 10, 2 );




/**
 * Check if the title is something other than the Article Id or empty. This is
 * usualy the case if it was modified
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @return boolean
 *
 * @since 1.0.0
 */

function sfw_article_has_custom_title( $article_selector = false ) {

  $article_id = sfw_get_article_id( $article_selector );
  $article_post = sfw_get_article_post( $article_id );

  $title = $article_post->post_title;
  $has_title = !empty( $title ) && $title != $article_id;

  return apply_filters(
    'sfw/article/has_custom_title',
    $has_title,
    $article_id,
    $article_post
  );
}




/**
 * Retrieve the Name of the current Article in the Sfw Loop.
 * this provides a fallback title consisting of
 * Design Name and / or ProductType Name.
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_article_name_fallback( $article_selector = false ) {

  $design_name 		    = sfw_get_design_name( sfw_get_article_default_design_id( $article_selector ) );
  $producttype_name 	= sfw_get_producttype_name( sfw_get_article_producttype_id( $article_selector ) );

  if( $design_name && $producttype_name ) {
    $title = $design_name.' '.$producttype_name;
  } else {
    $title = $producttype_name;
  }

  return apply_filters('sfw/article/name/fallback', $title, $design_name, $producttype_name );
}




/**
 * Echoes the article name fallback
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_article_name_fallback( $article_selector = false ) {

	echo sfw_get_article_name_fallback( $article_selector );
}




/**
 * Retrieve the default appearance id for the article
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_get_article_appearance_id( $article_selector = false  ) {

  return !empty( $article = sfw_get_article( $article_selector ) ) ? (int) $article->product->appearance->id : false;
}




/**
 * Echoes the default appearance id for the article
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_article_appearance_id( $article_selector = false ) {

	echo sfw_get_article_appearance_id( $article_selector );
}




/**
 * Get the corresponding appearance node from the producttype object
 *
 * @see sfw_get_producttype_appearance
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_article_appearance( $article_selector = false  ) {

	return sfw_get_producttype_appearance( sfw_get_article_appearance_id( $article_selector ) );
}




/**
 * Retrieve the current articles appearance name
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 * @return string|false
 *
 * @since 1.0.0
 */

function sfw_get_article_appearance_name( $article_selector = false  ) {

	return sfw_get_producttype_appearance_name( sfw_article_appearance_id( $article_selector ) );
}




/**
 * Echo the current articles appearance name
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_article_appearance_name( $article_selector = false ) {

	echo sfw_get_article_appearance_name( $article_selector );
}




/**
 * Retrieve the article default View
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 * @return int
 *
 * @since 1.0.0
 */

function sfw_get_article_view( $article_selector = false ) {

  return !empty( $article = sfw_get_article( $article_selector ) ) ? (int) $article->product->defaultValues->defaultView->id : false;
}




/**
 * Echoes the article default View
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_article_view( $article_selector = false ) {

	echo sfw_get_article_view( $article_selector );
}




/**
 * Test if a default design exists
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @return boolean
 *
 * @since 1.0.0
 */

function sfw_article_has_default_design( $article_selector = false ) {
  return !empty( $article = sfw_get_article( $article_selector ) )
    ? property_exists( $article->product->defaultValues->defaultDesign, 'id' )
    : false;
}




/**
 * Retrieve the article default design
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_get_article_default_design_id( $article_selector = false ) {

  return sfw_article_has_default_design( $article_selector ) && !empty( $article = sfw_get_article( $article_selector ) )
    ? (string) $article->product->defaultValues->defaultDesign->id
    : false;
}




/**
 * Echo the article default design
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_article_default_design_id( $article_selector = false ) {
	echo sfw_get_article_default_design_id( $article_selector );
}




/**
 * Retrieve the article producttype id
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @return int|false
 *
 * @since 1.0.0
 */

function sfw_get_article_producttype_id( $article_selector = false ) {

  $article_id = sfw_get_article_id( $article_selector );
  $article_post = sfw_get_post( 'article', $article_id );

  return sfw_is_wp_post( $article_post )
    ? sfw_get_producttype_id( $article_post )
    : false;
}




/**
 * echo the article producttype id
 *
 * @param  mixed $article_selector See sfw_get_article_id()
 *
 * @since 1.0.0
 */

function sfw_article_producttype_id( $article_selector = false ) {
	echo sfw_get_article_producttype_id( $article_selector );
}

