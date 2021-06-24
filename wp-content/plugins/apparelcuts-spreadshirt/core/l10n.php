<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Loads the core translations
 *
 * @ignore
 * @since  1.0.0
 */

function sfw_maybe_load_textdomain() {


  $root = basename( sfw_path( ) );

  if( $root === 'source' ) {
  }


  if( !sfw_constant('LOADED_TEXTDOMAIN') ) {
    sfw_load_plugin_textdomain( 'apparelcuts-spreadshirt' );
    //sfw_load_plugin_textdomain( 'sfw', 'spreadshirt-for-wordpress' );

    sfw_define('LOADED_TEXTDOMAIN', true );
  }

}

/*
 * @todo
 */
add_action( 'plugins_loaded', 'sfw_maybe_load_textdomain');
sfw_maybe_load_textdomain();





/**
 * Helper for loading plugin textdomain
 *
 * @ignore
 * @since  1.0.0
 */

function sfw_load_plugin_textdomain( $domain, $file = false ) {

 /**
  * Filters a plugin's locale.
  *
  * @since 3.0.0
  *
  * @param string $locale The plugin's current locale.
  * @param string $domain Text domain. Unique identifier for retrieving translated strings.
  */
 $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );

 $file = $file ?: $domain;
 $mofile = $file . '-' . $locale . '.mo';

 // Try to load from the languages directory first.
 if ( load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile ) ) {
   return true;
 }

 $path = sfw_path( 'languages' );

 return load_textdomain( $domain, $path . '/' . $mofile );
}






/**
 * Helps passing string through translation
 *
 * Uses call_user_func so that calls to __ ( ) do not get picked up by gettext parser
 *
 * @ignore
 * @since  1.0.0
 */

function sfw___( $string  ) {

  sfw_maybe_load_textdomain();

  return is_string( $string ) ? call_user_func( '__', $string, 'apparelcuts-spreadshirt' ) : $string;
}




/**
 * Helps passing string through translation
 *
 * Uses call_user_func so that calls to _x ( ) do not get picked up by gettext parser
 *
 * @ignore
 * @since  1.0.0
 */

function sfw__x( $string, $context  ) {

  sfw_maybe_load_textdomain();

  return is_string( $string ) ? call_user_func( '_x', $string, $context, 'apparelcuts-spreadshirt' ) : $string;
}




/**
 * Translates some properties of ACF Fields
 *
 * @see gulp/acf.js Task make_acf_translatable
 * @since  1.0.0
 */

function sfw_acf_translate_field( $field ) {

  if( !empty( $field['label'] ) )
    $field['label'] = sfw__x( $field['label'], 'acf' );

  if( !empty( $field['instructions'] ) )
    $field['instructions'] = sfw__x( $field['instructions'], 'acf' );

  if( !empty( $field['message'] ) )
    $field['message'] = sfw__x( $field['message'], 'acf' );


  if( !empty( $field['choices'] ) ) {
    if( is_array($field['choices']) ) foreach( $field['choices'] as $key => $choice ){
      $field['choices'][$key] = sfw__x( $choice, 'acf' );
    }
  }

  return $field;
}

add_filter( 'acf/prepare_field', 'sfw_acf_translate_field', 100 );




/**
 * Translates some properties of ACF Fieldgroups
 *
 * @see gulp/acf.js Task make_acf_translatable
 * @since  1.0.0
 */

function sfw_acf_translate_fieldgroup( $field_group ) {

  if( is_admin() && !empty( $field_group['title'] ) ) {

    $field_group['title'] = sfw__x( $field_group['title'], 'acf' );
    $field_group['title'] = str_replace( 'Spreadshirt - ', '', $field_group['title'] );

  }


  return $field_group;
}

add_filter( "acf/validate_field_group", 'sfw_acf_translate_fieldgroup', 100 );