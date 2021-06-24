<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Sync printtype before article
 *
 * @ignore
 * @since 1.0.0
 */

function _sfw_hook_sync_printtype_before_article( $bool, $entity, $article_id ){

  if( true !== $bool  )
    return $bool;

  return sfw_sync_article_printtypes( $article_id );

}




/**
 * Syncs all Printtypes from an Article/Product
 *
 * @param  string $article_id
 *
 * @return true|WP_Error
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_sync_article_printtypes( $article_id ) {


  if( is_wp_error( $article = sfw_get_article( $article_id, false, true ) ) )
    return $article;


  // make sure the product exists because sfw_product_get_all_printtype_ids is not reliable for this
  if( is_wp_error( $product = sfw_get_product( $article->product->id, false, true ) ) )
    return $product;


  // make sure all Designs are in sync
  $printtype_ids = sfw_product_get_all_printtype_ids( $product->id );


  // no problemo
  return sfw_sync_items( 'printtype', $printtype_ids );

}




/**
 * Adds Printtype Terms to Article on sync
 *
 * @ignore
 * @since 1.0.0
 */

function _sfw_hook_sync_article_add_printtype_terms( $article_post, $article_id, $entity ){


  // bail
  if( !sfw_is_wp_post( $article_post ) )
    return $article_post;


  // check if any term already exists
  //@todo should we double check if producttypes match?
  if( sfw_terms_exist( sfw_get_entity_taxonomy( 'printtype' ), $article_post ) )
    return $article_post;


  // get article
  if( is_wp_error( $article = sfw_get_article( $article_id, false, true ) ) )
    return $article;


  // make sure all Designs are in sync
  $printtype_ids = sfw_product_get_all_printtype_ids( $article->product->id );


  // bail
  if( empty( $printtype_ids ) )
    return $article_post;

  // add printtypes
  foreach( $printtype_ids as $printtype_id ) {

    // set the term
    $maybe_error = sfw_set_post_term(
      sfw_get_term( 'printtype', $printtype_id ),
      $article_post,
      true
    );

    if( is_wp_error( $maybe_error ) )
      return sfw_pass_error( $maybe_error );
  }



  return $article_post;
}




/**
 * Create a Term for an PrintType
 *
 * @see Hooks related to wp_insert_term for customization
 *
 * @param  [type] $printtype_id - a Spreadshirt PrintType Id
 *
 * @return Object of type WP_Error or $term on success
 *
 * @since 1.0.0
 */

function sfw_create_printtype( $printtype_id ) {

  // bail
  if( empty( $printtype_id ) )
    return sfw_novalue_error( '$printtype_id' );

	// get printtype resource or fail
	if( is_wp_error( $printtype = sfw_get_printtype( $printtype_id, false, true ) ) )
		return $printtype;


	$_termcfg = wp_insert_term(
	  (string) $printtype->name,
    sfw_get_entity_taxonomy( 'printtype' )
	);

	if( is_wp_error( $_termcfg ) )
		return sfw_pass_error( $_termcfg );

	add_term_meta(
    $_termcfg['term_id'],
    sfw_get_entity_wp_metakey( 'printtype' ),
    $printtype_id,
    true
  );

  $term = get_term( $_termcfg['term_id'] );

  // fill entity cache
  sfw_maybe_cache_object_spreadshirt_ids( $term );
  $entity_cache = SFW_Remote_Cache_Entity::get_instance( 'printtype', $printtype_id );
  $expire = apply_filters( 'sfw/printtype/expire', WEEK_IN_SECONDS );
  $entity_cache->set( $printtype, $expire );

	return $term;

}
