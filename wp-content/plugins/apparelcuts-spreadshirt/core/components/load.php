<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
 * Maybe cache after the query
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_the_posts_cache_ids( $posts ) {

  if( !empty( $posts ) )
    sfw_maybe_cache_object_spreadshirt_ids( $posts );

  return $posts;
}

add_filter( 'the_posts', '_sfw_hook_the_posts_cache_ids', 10, 2 );




/**
 * Optimizes caching of Articles spreadshirt ids
 *
 * Primes an articles design ids. All primed designs will be requested at once from the database, as
 * soon as on the designs is actually used.
 *
 * @param  WP_Post $post
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_maybe_cache_product_related_data( $post ) {

  if( !sfw_is_wp_post( $post ) || !sfw_is_object_meta_loaded( $post ) )
    return;


  // prime design ids

  $designs = get_post_meta( $post->ID, sfw_get_entity_wp_metakey( 'design' ) );

  if( !empty( $designs ) ) {

    $designs = sfw_make_array( $designs );

    foreach( $designs as $design ) {

      sfw_prime_entity( 'design', $design );
    }

  }



  // cache producttype id

  $producttype_terms = get_the_terms( $post->ID, sfw_get_entity_posttype('producttype') );
  $producttype_id    = get_post_meta( $post->ID, '_producttype-id', true );

  if( is_array( $producttype_terms ) && !empty( $producttype_terms ) ) {

    sfw_id_cache()->set(
      sfw_get_entity_posttype('producttype'),
      $producttype_terms[0]->term_id,
      $producttype_id,
      sfw_get_entity_wp_metakey('producttype')
    );
  }

}

add_action( 'sfw/sfw_maybe_cache_object_spreadshirt_ids/article', '_sfw_hook_maybe_cache_product_related_data' );




/**
 * Triggers preload of term meta on some requests.
 *
 * This way the spreadshirt ids of objects can be cached, before other scripts try to
 * request them directly from the database. In many cases this leads to a remarkable
 * reduction in database queries.
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_preload_term_metadata( ) {

  if( is_admin() )
    return;

  global $wp_query;

  if( !$wp_query->is_main_query() )
    return;

  if( empty( $wp_query->post ) )
    return;

  $queried_object = $wp_query->get_queried_object();


  if( $queried_object instanceof WP_Post_Type ) {

    if( ! $entity = sfw_get_entity_by_posttype( $queried_object->name ) )
      return;

    if( sfw_lazy_load_term_metadata_now() )
      sfw_maybe_cache_object_spreadshirt_ids( $wp_query->posts );

  }
  elseif( $wp_query->is_single && $queried_object instanceof WP_Post ) {

    if( ! $entity = sfw_get_entity_by_posttype( $queried_object->post_type ) )
      return;

    if( sfw_lazy_load_term_metadata_now() )
      sfw_maybe_cache_object_spreadshirt_ids( $wp_query->posts );
  }
  elseif( $wp_query->is_tax && $wp_query->query_vars['taxonomy'] ) {

    if( ! $entity = sfw_get_entity_by_taxonomy( $wp_query->query_vars['taxonomy'] ) )
      return;

    if( sfw_is_wp_term( $queried_object ) )
      sfw_maybe_cache_object_spreadshirt_ids( $queried_object );

  }

}

add_action( 'wp', '_sfw_hook_preload_term_metadata' );