<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Registers a new dynamic page type
 *
 * @param  array $page
 * @return WP_Error|true
 * @since  1.0.0
 */

function sfw_register_dynamic_page( $page ) {

  return sfw_dynamic_page_controller()->register_page( $page );
}




/**
 * return dynamic page settings for post
 *
 * @param  int|WP_Post $post
 * @return false|array
 * @since  1.0.0
 */

function sfw_get_page_by_post( $post ) {

  return sfw_dynamic_page_controller()->get_dynamic_page_by_post( $post );
}




/**
 * Retrieve a wp page id for dynamic page
 *
 * @param  string $slug
 * @return false|null
 * @since  1.0.0
 */

function sfw_get_page_id( $page_slug ) {

  return sfw_dynamic_page_controller()->get_page_id( $page_slug );
}




/**
 * Retrieve permalink for page
 *
 * @param  string $slug
 * @return string
 * @since  1.0.0
 */

function sfw_get_page_link( $page_slug ) {

  $link = get_option( 'sfw-page-'.$page_slug );

  return empty( $link )
    ? get_permalink( sfw_get_page_id( $page_slug ) )
    : $link;
}




/**
 * checks if page matches layout and if the layout is valid
 *
 * @param  string|array|false $page_slugs
 * @param  boolean $post_id
 * @return boolean
 * @since  1.0.0
 */

function sfw_is_page( $check_page_slugs = false, $post_id = false ) {

  return sfw_dynamic_page_controller()->is_page( $check_page_slugs, $post_id );
}




