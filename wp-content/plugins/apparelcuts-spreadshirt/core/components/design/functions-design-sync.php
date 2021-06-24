<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
 * Syncs article designs before the article gets synced
 *
 * @ignore
 * @return true|WP_Error
 * @since 1.0.0
 */

function _sfw_hook_sync_designs_before_article( $bool, $entity, $article_id, $action ){

  if( true !== $bool  )
    return $bool;

  return sfw_sync_article_designs( $article_id );

}




/**
 * syncs all designs assiciated with an article
 *
 * @param string $article_id
 * @return true|WP_Error
 * @since 1.0.0
 */

function sfw_sync_article_designs( $article_id ) {


  if( is_wp_error( $article = sfw_get_article( $article_id, false, true ) ) )
    return $article;


  // make sure the product exists because sfw_product_get_all_design_ids is not reliable for this
  if( is_wp_error( $product = sfw_get_product( $article->product->id, false, true ) ) )
    return $product;


  // make sure all Designs are in sync
  $design_ids = sfw_product_get_all_design_ids( $product->id );

  // no problemo
  return sfw_sync_items( 'design', $design_ids );

}




/**
* Create a Post and Metadate for an Design
*
* @param $design_id - a Spreadshirt Design Id
* @return Object of type WP_Error or $post on success
* @since 1.0.0
*/

function sfw_create_design( $design_id ) {


  // bail
  if( empty( $design_id ) )
    return sfw_novalue_error( '$article_id' );



  // get article resource or fail
  if( is_wp_error( $design = sfw_get_design( $design_id, true, true ) ) )
    return $design;



  // fill postdata
	$postdata = array(
		'post_type' 	=> 'sfw-design',
		'post_title'  => $design->fileName,
		'post_content'  => (string) $design->description,
		'post_status'   => 'publish'
	);


  // maybe get name
  if( property_exists( $design, 'name' ) && !empty( $design->name ) )
    $postdata['post_title'] = $design->name;


  // maybe get description
  if( property_exists( $design, 'description' ) && !empty( $design->description ) )
    $postdata['post_content'] = $design->description;



  $postdata = apply_filters( 'sfw/design/wp_insert_post', $postdata, $design );



  // create post
	$post_id = wp_insert_post( $postdata );




  // bail
  if( is_wp_error( $post_id ) )
    return sfw_pass_error( $post_id );

	if( empty( $post_id ) )
    return sfw_create_error( 'failed', "Couldn't create Post.", 'sfw-design' );


  // get the post
	$post = get_post( $post_id );


  // background?
  $background_color = property_exists( $design, 'backgroundColor')
    ? $design->backgroundColor
    : false;
  $background_color = apply_filters('sfw/create/design/bg', $background_color, $design );


  // add basic postdata
  add_post_meta( $post->ID, sfw_get_entity_wp_metakey( 'design' ),  $design->id,                  true );
  add_post_meta( $post->ID, '_spreadshirt-id',                      $design->id,                  true );
  add_post_meta( $post->ID, '_preview-url',                         $design->resources[0]->href,  true );
  add_post_meta( $post->ID, '_iswhite',                             (bool) $background_color,     true );
  add_post_meta( $post->ID, '_bg',                                  (string) $background_color,   true );


  // fill entity cache
  sfw_maybe_cache_object_spreadshirt_ids( $post );
  $entity_cache = SFW_Remote_Cache_Entity::get_instance( 'design', $design->id );
  $expire = apply_filters( 'sfw/design/expire', WEEK_IN_SECONDS );
  $entity_cache->set( $design, $expire );

  return $post;

}




/**
 * Adds Design Ids as meta to Article Post
 *
 * @ignore
 * @since 1.0.0
 */

function _sfw_hook_sync_article_add_design_ids( $article_post, $article_id, $entity ){

  // bail
  if( !sfw_is_wp_post( $article_post ) )
    return $article_post;


  // check if design_ids are already set
  $design_ids = get_post_meta( $article_post->ID, sfw_get_entity_wp_metakey( 'design' ), false );


  // bail
  if( !empty( $design_ids ) )
    return $article_post;


  // get producttype id
  if( is_wp_error( $article = sfw_get_article( $article_id, false, true ) ) )
    return $article;


  // product_id
  $product_id = $article->product->id;


  // retrieve design_ids
  $design_ids = sfw_product_get_all_design_ids( $product_id );


  // bail
  if( empty( $design_ids ) || !is_array( $design_ids ) )
    return $article_post;


  foreach( $design_ids as $design_id ) {
	  add_post_meta(
      $article_post->ID,
      sfw_get_entity_wp_metakey( 'design' ),
      $design_id,
      false
    );
  }

  return $article_post;


}

