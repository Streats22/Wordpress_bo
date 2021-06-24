<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
 * Adds brand term to Article
 *
 * @param WP_Post $article_post
 * @param string $article_id
 * @param SFW_Entity $entity
 *
 * @since 1.0.0
 */

function _sfw_hook_sync_article_add_brand_terms( $article_post, $article_id, $entity ){


  // bail
  if( !sfw_is_wp_post( $article_post ) )
    return $article_post;


  // check if any term already exists
  //@todo should we double check if producttypes match?
  if( sfw_terms_exist( sfw_get_entity_taxonomy( 'brand' ), $article_post ) )
    return $article_post;


  // get article
  if( is_wp_error( $article = sfw_get_article( $article_id, false, true ) ) )
    return $article;


  $producttype_id = $article->product->productType->id;


  // get producttype
  if( is_wp_error( $producttype = sfw_get_producttype( $producttype_id, false, true ) ) )
    return $producttype;


  // set the term
  $maybe_error = sfw_set_post_term(
    sfw_get_term( 'brand', $producttype->brand ),
    $article_post,
    false
  );


  if( is_wp_error( $maybe_error ) )
    return $maybe_error;


  return $article_post;

}
