<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
 * Mark Spreadshirt Ids that will most likeley have to be be queried later
 *
 * @param  SFW_Entity|string $entity
 * @param  string|array $spreadshirt_id Spreadshirt id for entity or array of ids
 * @since  1.0.0
 */

function sfw_prime_entity( $entity, $spreadshirt_ids ) {

  $entity = sfw_get_entity( $entity );

  if( !sfw_is_entity( $entity ) )
    return;

  $cache_key =  'primed_' . $entity->name();
  $primed_spreadshirt_ids = sfw_var( $cache_key );
  $spreadshirt_ids = sfw_make_array( $spreadshirt_ids );

  if( !$primed_spreadshirt_ids )
    $primed_spreadshirt_ids = $spreadshirt_ids;
  else
    $primed_spreadshirt_ids = array_merge( $primed_spreadshirt_ids, $spreadshirt_ids );

  sfw_var( $cache_key, $primed_spreadshirt_ids );
}





/**
 * Loads primed entity items now
 *
 * @param  SFW_Entity|string $entity
 * @param  boolean $update_meta_cache
 * @param  boolean $update_term_cache
 * @since  1.0.0
 */

function sfw_load_primed( $entity, $args = array() ) {

  $entity = sfw_get_entity( $entity );

  if(!sfw_is_entity( $entity  ))
    return;

  $cache_key = 'primed_' . $entity->name();
  $primed_spreadshirt_ids = sfw_var( $cache_key );


  // bail if nothing to load
  if( empty( $primed_spreadshirt_ids ) )
    return;

  // remove known ids
  $primed_spreadshirt_ids = array_filter( array_unique( $primed_spreadshirt_ids ), function( $spreadshirt_id ) use ( $entity ){
    return is_null( sfw_id_cache()->get_wp_id( $entity->wp_subtype, $spreadshirt_id, $entity->wp_metakey ) );   // look for cached terms
  });

  // check again
  if( empty( $primed_spreadshirt_ids ) )
    return;

  $items = false;


  // query posts
  if( $entity->is_post() ) {

    $args = wp_parse_args( $args, array(
      'meta_key' => $entity->wp_metakey,
      'meta_compare' => 'IN',
      'meta_value' => $primed_spreadshirt_ids,
      'posts_per_page' => -1,
      'post_status' => implode( ',', get_post_stati() ),
      'post_type' => $entity->wp_subtype,
      'update_post_term_cache' => false,
      'update_post_meta_cache' => true
    ));

    $items = get_posts( $args );

  }
  // or query terms
  elseif( $entity->is_term()) {

    $args = wp_parse_args( $args, array(
      'meta_key' => $entity->wp_metakey,
      'meta_compare' => 'IN',
      'meta_value' => $primed_spreadshirt_ids,
      //'number' => 5,
      'hide_empty' => false,
      'taxonomy' => $entity->wp_subtype,
      'update_term_meta_cache' => true
    ));

    $items = get_terms( $args );

  }
  // fill id cache
  if( !empty( $items ) && !is_wp_error( $items ) )
    sfw_maybe_cache_object_spreadshirt_ids( $items );

}



/**
 * Checks if Metadata for Object is in the cache
 *
 * @param  WP_Post|WP_Term|int $mixed
 * @param  mixed $objecttype Optional if first parameter is WP_Post or WP_Term
 * @return boolean
 * @since  1.0.0
 */

function sfw_is_object_meta_loaded( $mixed, $objecttype = false ) : bool {

  if( sfw_is_wp_post( $mixed ) ) {

    $objecttype = 'post';
    $id = $mixed->ID;
  }
  elseif( sfw_is_wp_term( $mixed ) ) {

    $objecttype = 'term';
    $id = $mixed->term_id;
  }
  else {
    $id = $mixed;
    $objecttype = $objecttype;
  }

  $found = false;

  wp_cache_get( $id, $objecttype.'_meta', false, $found );

  return $found;
}




/**
 * Maybe cache spreadshirt ids of posts or terms
 *
 * @param  array|WP_Post|WP_Term $posts A list of post objects
 * @since  1.0.0
 */

function sfw_maybe_cache_object_spreadshirt_ids( $objects ) {

  $objects = sfw_make_array( $objects );

  foreach( $objects as $object ) {

    $entity = sfw_get_wpobject_entity( $object );

    if( false === $entity )
      continue;

    // we only use meta that is already there
    if( !sfw_is_object_meta_loaded( $object ) ) {
      continue;
    }

    sfw_id_cache()->set(
      $entity->wp_subtype,
      $object instanceof WP_Post ? $object->ID : $object->term_id,
      sfw_get_object_metadata( $object, $entity->wp_metakey, true ),
      $entity->wp_metakey
    );

    if( $entity->is_post()) {

      // go through all taxnomies
      foreach( get_object_taxonomies( $object ) as $taxonomy ) {

        // skip non entities
        if( !$taxonomy_entity = sfw_get_entity_by_taxonomy( $taxonomy ) )
          continue;

        // get terms
        $terms = get_the_terms( $object->ID, $taxonomy );

        // maybe cache term ids
        if( is_array( $terms ) && !empty( $terms ) )
          sfw_maybe_cache_object_spreadshirt_ids( $terms );
      }
    }

    do_action( 'sfw/sfw_maybe_cache_object_spreadshirt_ids/'.$entity->name(), $object );

  }
}




/**
 * Force Lazyloader to load queued term meta
 *
 * @ignore
 * @since  1.0.0
 */

function sfw_lazy_load_term_metadata_now() {

  $lazyloader = wp_metadata_lazyloader();

  if( method_exists( $lazyloader, 'lazyload_term_meta' ) ) {

    $lazyloader->lazyload_term_meta( null );
    return true;
  }
  return false;
}





