<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
 * Create a Post and Metadate for an Article
 *
 * @param  string $article_id  - a Spreadshirt Article Id
 *
 * @return Object of type WP_Error or $post on success
 *
 * @since 1.0.0
 */

function sfw_create_article( $article_id ) {


  // bail
  if( empty( $article_id ) )
    return sfw_novalue_error( '$article_id' );



  // get article resource or fail
  if( is_wp_error( $article = sfw_get_article( $article_id, true, true ) ) )
    return $article;



  $post_status = get_field( 'sfw-sync-product-post-status', 'option' );


  // fill postdata
	$postdata = array(
		'post_type' 	  => 'sfw-product',
		'post_title'    => $article->id,
		'post_content'  => '',
		'post_status'   => $post_status ?: 'publish'
	);


  // maybe get name
  if( property_exists( $article, 'name' ) && !empty( $article->name ) )
    $postdata['post_title'] = $article->name;


  // maybe get description
  if( property_exists( $article, 'description' ) && !empty( $article->description ) )
    $postdata['post_content'] = $article->description;



  $postdata = apply_filters( 'sfw/article/wp_insert_post', $postdata, $article );



  // create post
	$post_id  = wp_insert_post( $postdata );



  //bail
  if( is_wp_error( $post_id ) )
    return sfw_pass_error( $post_id );

	if( empty( $post_id ) )
    return sfw_create_error( 'failed', "Couldn't create Post.", 'sfw-product' );



  // get the post
	$post = get_post( $post_id );


  // add default design id, can be blank if no design exists
  $default_design_id = isset( $article->product->defaultValues->defaultDesign->id )
    ? $article->product->defaultValues->defaultDesign->id
    : '';

  // add basic postdata
  add_post_meta( $post->ID, sfw_get_entity_wp_metakey( 'article' ), $article->id,                                      true );
  //add_post_meta( $post->ID, '_spreadshirt-id',                      $article->product->id,                             true );
  add_post_meta( $post->ID, '_product-id',                          $article->product->id ,                            true );
  add_post_meta( $post->ID, '_producttype-id',                      $article->product->productType->id,                true );
  add_post_meta( $post->ID, '_view-id',                             $article->product->defaultValues->defaultView->id, true );
  add_post_meta( $post->ID, '_preview-url',                         $article->resources[0]->href,                      true );
  add_post_meta( $post->ID, '_default-design-id',                   $default_design_id,                                true );


  // fill entity cache
  sfw_maybe_cache_object_spreadshirt_ids( $post );
  $entity_cache = SFW_Remote_Cache_Entity::get_instance( 'article', $article->id );
  $expire = apply_filters( 'sfw/article/expire', WEEK_IN_SECONDS );
  $entity_cache->set( $article, $expire );

  return $post;

}
