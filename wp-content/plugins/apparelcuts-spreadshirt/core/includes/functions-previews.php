<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
 * Previews are an alternative way to show Images.
 * They're very effective but less reliable. It's not recommended to use Previews within your Theme.
 *
 * @since 1.0.0
 */




/**
 * Create a Preview Url for post from preview url in meta
 *
 * @param  WP_Post $post
 * @param  array  $args Array with the desired image dimensions array( width, height )
 *
 * @return string url
 *
 * @since  1.0.0
 */

function sfw_create_preview_url( $post, $args = array( 100, 100 ) ) {

  if( empty( $url = get_field( '_preview-url', $post ) ) ) {
    return false;
  }

  if( count( $args ) == 2 && isset( $args[0] ) && isset( $args[1] ) && is_int( $args[0] ) &&  is_int( $args[1] ) ) {
    $args = array( 'width' => $args[0], 'height' => $args[1] );
  }

  return $url.'?'.http_build_query( $args );
}




/**
 * Creates Preview img html
 *
 * @param  WP_Post $post
 * @param  array  $args Array with the desired image dimensions array( width, height )
 *
 * @return string html
 *
 * @since  1.0.0
 */

function sfw_create_preview_img( $post, $args = array( 100, 100 ) ) {

  return '<img src="'.sfw_create_preview_url( $post, $args ).'"/>';
}




/**
 * Echoes a preview image
 *
 * @param  WP_Post $post
 * @param  array  $args Array with the desired image dimensions array( width, height )
 *
 * @return string html
 *
 * @since  1.0.0
 */

function sfw_preview_img( $post, $args = array( 100, 100 ) ) {

  echo sfw_create_preview_img( $post, $args );
}



