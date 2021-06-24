<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Syncs producttype before article sync
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_sync_producttype_before_article( $bool, $entity, $article_id ){

  if( true !== $bool  )
    return $bool;

  if( is_wp_error( $article = sfw_get_article( $article_id, false, true ) ) )
    return $article;

  $result = sfw_sync_item( 'producttype', $article->product->productType->id );

  return is_wp_error( $result ) ? $result : true;

}




/**
 * Syncs producttype terms after article sync
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_sync_article_add_producttype_terms( $article_post, $article_id, $entity ){


  // bail
  if( !sfw_is_wp_post( $article_post ) )
    return $article_post;


  // check if any term already exists
  //@todo should we double check if producttypes match?
  if( sfw_terms_exist( sfw_get_entity_taxonomy( 'producttype' ), $article_post ) )
    return $article_post;


  // get producttype id
  if( is_wp_error( $article = sfw_get_article( $article_id, false, true ) ) )
    return $article;

  $producttype_id = $article->product->productType->id;


  // set the term
  $maybe_error = sfw_set_post_term(
    sfw_get_term( 'producttype', $producttype_id ),
    $article_post,
    false
  );

  if( is_wp_error( $maybe_error ) )
    return $maybe_error;


  return $article_post;


}




/**
* Create a Term for an ProductType
*
* @param $producttype_id - a Spreadshirt ProductType Id
* @return Object of type WP_Error or $term on success
* @see Hooks related to wp_insert_term for customization
* @since 1.0.0
*/

function sfw_create_producttype( $producttype_id ) {

  // bail
  if( empty( $producttype_id ) )
    return sfw_novalue_error( '$producttype_id' );

  // get producttype resource or fail  ;
  if( is_wp_error( $producttype = sfw_get_producttype( $producttype_id, true, true ) ) )
    return $producttype;


  $_termcfg = wp_insert_term(
  	(string) $producttype->name,
    sfw_get_entity_taxonomy( 'producttype' ),
    array(
      'slug'        => sanitize_title( (string) $producttype->name ),
      //'description' => (string) $producttype->description,
    )
  );


  if( is_wp_error( $_termcfg ) )
    return sfw_pass_error( $_termcfg );

  add_term_meta(
    $_termcfg['term_id'],
    sfw_get_entity_wp_metakey( 'producttype' ),
    $producttype_id,
    true
  );

  $term = get_term( $_termcfg['term_id'] );

  // fill entity cache
  sfw_maybe_cache_object_spreadshirt_ids( $term );
  $entity_cache = SFW_Remote_Cache_Entity::get_instance( 'producttype', $producttype->id );
  $expire = apply_filters( 'sfw/producttype/expire', WEEK_IN_SECONDS );
  $entity_cache->set( $producttype, $expire );

  return $term;

}



