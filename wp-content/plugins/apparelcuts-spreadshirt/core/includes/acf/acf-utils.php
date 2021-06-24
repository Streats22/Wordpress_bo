<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Helper function for Advanced Custom Fields
 *
 * @ignore
 * @since 1.0.0
 */


require_once( trailingslashit( __DIR__ ) . 'fallback.php');



/**
 * Caches relation between a field key and its name
 *
 * @ignore
 * @since  1.0.0
 */

function sfw_remember_field_key( $field_key, $name ) {

  $field_keys = sfw_var( 'field_keys' );

  if( !is_array( $field_keys ) ) $field_keys = array();

  $field_keys[ $name ] = $field_key;

  sfw_var( 'field_keys', $field_keys );

}




/**
 * Retrieve a field_key
 *
 * @param  string $name The field name
 *
 * @return string The field_key
 *
 * @ignore
 * @since  1.0.0
 */

function sfw_field_key( $name ) {

  $field_keys = sfw_var( 'field_keys' );

  return isset( $field_keys[ $name ] )
    ? $field_keys[ $name ]
    : '';

}




/**
 * Retrieve a fields name
 *
 * @param  string $field_key An ACF Field key
 *
 * @return string The fields name
 *
 * @ignore
 * @since  1.0.0
 */

function sfw_field_name( $field_key ) {

  $field_keys = sfw_var( 'field_keys' );

  $name = array_search( $field_key, $field_keys );

  return $name !== false
    ? $name
    : '';

}




/**
 * Returns Instructions marking options for developers and advanced users
 *
 * @return string
 * @since  1.0.0
 */

function sfw_acf_developer_instructions(  ) {

	return '<span class="sfw-be-careful">'.
					__("This is a hidden setting, please do not change it unless you know exactly what you're doing!", 'apparelcuts-spreadshirt' ).
					'</span>';
}




/**
 * Sets ACF save point for a specific fieldgroup
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_add_acf_group_save_point( $group_key, $path = false, $auto_private = true ) {

  // bail
  if( !is_admin() )
    return;


  if( false === $path )
    $path = sfw_path() . '/assets/acf';


  // set the private parameter
  if( $auto_private )
    add_filter( 'acf/validate_field_group', function ( $field_group ) use ( $group_key ) {

      if( $group_key === $field_group['key'] )
        $field_group['private'] = sfw_constant( 'SHOW_ACF' );

      return $field_group;
    } );


  add_filter( 'acf/settings/save_json', function( $default_save_path ) use ( $path, $group_key ) {


    global $_POST;


    if( !isset( $_POST['acf_field_group'] ) )
      return $default_save_path;


    if( $group_key === $_POST['acf_field_group']['key'] )
      $default_save_path = $path;


    return $default_save_path;

  } );


  return $path;

}




/**
 * Sets an ACF load point
 *
 * @ignore
 * @since 1.0.0
 */

function sfw_add_acf_load_point( $path = false ) {

  if( false === $path )
    $path = sfw_path() . '/assets/acf';


  add_filter( 'acf/settings/load_json', function( $paths ) use ( $path ) {

    $paths[] = $path;

    return $paths;
  } );

  return $path;

}




/**
 * Checks for the field group edit screen
 *
 * @return boolean
 * @since  1.0.0
 */

function sfw_is_acf_group_edit_screen() {

  if( !is_admin() || !function_exists( 'get_current_screen' ) )
    return false;

  $screen = get_current_screen();

  return $screen->post_type === 'acf-field-group';
}




/**
 * If we can use acf pro functionality
 *
 * @return bool
 * @since  1.0.0
 */

function sfw_can_use_acf_pro() : bool {

  return defined('ACF_PRO') && constant( 'ACF_PRO' ) === true;
}




/**
 * Sets autoload to no
 *
 * @param  string|array $names Options to unautoload
 * @since  1.0.0
 */

function sfw_acf_unautoload_options( $names ) {

	$names = sfw_make_array( $names );

	global $wpdb;

	$options_names = array();
	foreach( $names as $name ) {
		$options_names[] = 'options_'.esc_sql( $name );
		$options_names[] = '_options_'.esc_sql( $name );
	}

	$options_names = array_unique( $options_names );

	$query = "UPDATE $wpdb->options SET autoload = 'no' WHERE option_name IN ('".implode("','", $options_names )."');";

	return $wpdb->query( $query );

}




/**
 * Adds content after rendered field forms
 *
 * @param  string $field_key
 * @param  callable|string $content
 * @since  1.0.0
 */

function sfw_acf_append_to_field( $field_key, $content, $priority = 15 ) {

  if( !is_admin() )
    return;


  /*
   * Add a "change options" warning to shop id and platform
   */
  add_action( 'acf/render_field/key='.$field_key, function( $field ) use ( $content, $field_key ){

    // @see https://support.advancedcustomfields.com/forums/topic/does-post_object-field-go-through-acfrender_field-twice/
    if ( did_action('acf/render_field/key='.$field_key) % 2 === 0 )
  		return;

    if( is_callable( $content ) ) {
      call_user_func( $content );
    }
    elseif( is_scalar( $content ) ) {
      echo $content;
    }

  }, $priority );

}