<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
* Check if the current screen matches value
*
* Consider that this function only works, if get_current_screen is available
*
* @param string $screen_id
* @return bool
* @since 1.0.0
*/

function sfw_current_screen_id_is( $screen_id ) : bool {

  if( ! is_admin() || !function_exists('get_current_screen'))
    return false;

  $current_screen = get_current_screen();

  return @$current_screen->id == $screen_id;
}




/**
 * Adds post type admin table column with content
 *
 * @param string  $posttype
 * @param string  $label
 * @param callable  $callback
 * @param int $priority
 * @since 1.0.0
 */

function sfw_add_admin_table_column( $posttype, $label, $callback, $priority = 15 ) {

  if( !is_admin())
    return;

  $key = 'sfw-'.$posttype.'-'.sanitize_key( $label );

  add_filter( "manage_{$posttype}_posts_columns", function( $columns ) use ( $key, $label, $priority ) {

    $columns[ $key ] = $label;

    return $columns;

  }, $priority );


  add_filter( "manage_{$posttype}_posts_custom_column", function( $column_name, $post_id ) use ( $key, $callback ) {

    if( $column_name === $key ) {
      call_user_func( $callback, $post_id );
    }

  }, 10, 2 );

}




/**
 * Adds taxonomy admin table column with content
 *
 * @param string  $taxonomy
 * @param string  $label
 * @param callable  $callback
 * @param int $priority
 * @since 1.0.0
 */

function sfw_add_taxonomy_table_column( $taxonomy, $label, $callback, $priority = 15 ) {

  if( !is_admin())
    return;

  $key = 'sfw-'.$taxonomy.'-'.sanitize_key( $label );

  add_filter( "manage_edit-{$taxonomy}_columns", function( $columns ) use ( $key, $label, $priority ) {

    $columns[ $key ] = $label;

    return $columns;

  }, $priority );


  add_filter( "manage_{$taxonomy}_custom_column", function( $content, $column_name, $term_id ) use ( $key, $callback ) {

    if( $column_name === $key ) {
      return call_user_func( $callback, $term_id, $content );
    }

    return $content;

  }, 10, 3 );

}




/**
 * Add Metabox
 *
 * Accepts Metabox args as array. Can be called directly.
 *
 * @param array $args
 * @since 1.0.0
 */

function sfw_add_meta_box( $args, $hooked = 'add_meta_boxes' ) {

  if( !is_admin() )
    return;

  $args = wp_parse_args( $args, array(
    'id' => null,
    'title' => null,
    'callback' => null,
    'screen' => null,
    'context' => 'advanced',
    'priority' => 'default',
    'callback_args' => null
  ));


  add_action( 'add_meta_boxes', function() use ( $args ){

    extract( $args );
    add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );

  });

}