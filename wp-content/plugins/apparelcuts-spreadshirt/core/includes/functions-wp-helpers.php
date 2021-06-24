<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Checks if param is of type WP_Post
 *
 * If Wordpress ever decides to implement such function, it will most
 * likely follow this syntax
 *
 *
 * @param $mixed
 * @return bool True if $mixed is instance of WP_Post
 * @since 1.0.0
 */

function sfw_is_wp_post( $mixed ) : bool {
  return $mixed instanceof WP_Post;
}





/**
* Checks if param is of type WP_Term
*
* @param $mixed
* @return bool True if $mixed is instance of WP_Term
* @since 1.0.0
*/

function sfw_is_wp_term( $mixed ) : bool {
  return $mixed instanceof WP_Term;
}




/**
 * Checks for WP_Term or WP_Post
 *
 * @param  mixed $mixed
 * @return boolean true if $mixed is WP_Post or WP_Term
 */

function sfw_is_wpobj( $mixed ) : bool {

  return sfw_is_wp_post( $mixed ) || sfw_is_wp_term( $mixed );
}




/**
 * Retrieve a term id
 *
 * @param  int|WP_Term $mixed A term id or WP_Term instance
 * @return int a term id
 * @since 1.0.0
 */

function sfw_get_term_id( $mixed ) {

  if( sfw_is_wp_term( $mixed ) )
    return $mixed->term_id;

  return $mixed;
}




/**
 * Same as get_term_meta, but accepts WP_Term as first parameter
 *
 * @param  int|WP_Term  $term_id term id or WP_Term
 * @param  string  $key
 * @param  boolean $single
 * @return mixed the terms meta
 * @see get_term_meta
 * @since 1.0.0
 */

function sfw_get_term_meta( $term_id, $key = '', $single = false ) {

  return get_term_meta( sfw_get_term_id( $term_id ), $key, $single );
}




/**
 * Retrieve a post id
 *
 * @param  int|WP_Post $mixed A post id or WP_Post instance
 * @return int a post id
 * @since 1.0.0
 */

function sfw_get_post_id( $mixed ) {

  if( $mixed instanceof WP_Post )
    return $mixed->ID;

  return $mixed;
}




/**
 * Same as get_post_meta, but accepts WP_Post as first parameter
 *
 * @param  int|WP_Post  $post_id  post id or WP_Post
 * @param  string  $key
 * @param  boolean $single
 * @return mixed the posts meta
 * @see get_post_meta
 * @since 1.0.0
 */

function sfw_get_post_meta( $post_id, $key = '', $single = false ) {

  return get_post_meta( sfw_get_post_id( $post_id ), $key, $single );
}




/**
 * Updates meta of post or term
 *
 * @param  WP_Post|WP_Term $object
 * @param  string $meta_key
 * @param  mixed $meta_value Any value
 * @return bool
 * @see update_metadata
 * @since 1.0.0
 */

function sfw_update_object_metadata( $object, $meta_key, $meta_value ) {

  if( sfw_is_wp_post( $object ) ) {
    return update_metadata( 'post', $object->ID, $meta_key, $meta_value );
  }
  elseif( sfw_is_wp_term( $object ) ) {
    return update_metadata( 'term', $object->term_id, $meta_key, $meta_value );
  }

  return false;
}




/**
 * Retrieve meta of post or term
 *
 * @param  WP_Post|WP_Term $object
 * @param  string $meta_key
 * @param  bool $single
 * @return mixed The meta data
 * @see get_metadata
 * @since 1.0.0
 */

function sfw_get_object_metadata( $object, $meta_key, $single = true ) {

  if( sfw_is_wp_post( $object ) ) {
    return get_metadata( 'post', $object->ID, $meta_key, $single );
  }
  elseif( sfw_is_wp_term( $object ) ) {
    return get_metadata( 'term', $object->term_id, $meta_key, $single );
  }

  return false;
}




/**
 * Retrieve the first term of taxonomy assocciated with a post
 *
 * @param string $taxonomy - a valid taxonomy name
 * @param string $post_id - post id or WP_Post
 * @return bool|WP_Term WP_Term or false if none is connected with the post
 * @since 1.0.0
 */

function sfw_get_primary_term( $taxonomy,  $post = false ) {

  $post    = get_post( $post );

  if( !sfw_is_wp_post( $post ) )
    return false;

	$terms 	 = get_the_terms( $post->ID, $taxonomy );

	return is_array( $terms ) && !empty( $terms )
    ? apply_filters( 'sfw/get_primary_term', $terms[0], $taxonomy, $post )
    : false;
}




/**
 * Checks if any terms of given taxonomy are tied to $post
 *
 * @param  string $taxonomy A valid
 * @param  int|WP_Post $post A post or post id
 * @return bool Returns true if any term is associated with post
 * @since 1.0.0
 */

function sfw_terms_exist( $taxonomy, $post ) {

  $post    = get_post( $post );

  if( !sfw_is_wp_post( $post ) )
    return false;

	$terms 	 = get_the_terms( $post->ID, $taxonomy );

	return is_array( $terms ) && !empty( $terms );
}




/**
 * adds a term to a post
 *
 * @param WP_Term  $term
 * @param WP_Post  $post
 * @param boolean $append wether to append the term or replace existing terms
 * @return WP_Error|bool
 * @since 1.0.0
 */

function sfw_set_post_term( $term, $post, $append = false ) {

  if( !sfw_is_wp_term( $term ) || !sfw_is_wp_post( $post ) )
    return sfw_value_error( [ $term, $post ]);

  // set post producttype term
	$terms = wp_set_post_terms(
    $post->ID,
    array( $term->term_id ),
    $term->taxonomy,
    $append
  );

  return is_array( $terms )
    ? true
    : sfw_error( 'Could not add term to post', array( $taxonomy, $term, $post, $append ) );

}




/**
 * Returns the Id for posts and terms
 *
 * @param  object $wpitem A post or term
 *
 * @return int|false
 *
 * @since  1.0.0
 */

function sfw_get_wpitem_id( $wpitem ) {

  if( sfw_is_wp_post( $wpitem ) )
    return $wpitem->ID;
  elseif( sfw_is_wp_term( $wpitem ) )
    return $wpitem->term_id;

  return false;
}




/**
 * Schedules a new event if it does not already exist
 *
 * @param  int $duration Duration after now
 * @param  string $hook
 * @param  array  $args
 * @since  1.0.0
 */


function sfw_schedule_single_event_throttled( $duration, $hook, $args = array() ) {

  if( !wp_next_scheduled( $hook, $args ) ) {
    sfw_debug( 'Schedule '.$hook );
    $timestamp = time() + $duration;
    wp_schedule_single_event( $timestamp, $hook, $args );
  }
}
