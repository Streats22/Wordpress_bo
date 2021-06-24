<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Retrieve plugin root path.
 *
 * @param  string $path Optional. A path relative to the plugin root.
 * @return string an absolute path
 * @since 1.0.0
 */

function sfw_path( $path = '' ) : string {
  return sfw_plugin()->path . $path;
}




/**
 * Retrieve plugin dir.
 *
 * @param  string $path Optional. A path relative to the plugin root.
 * @return string       A plugin dir link.
 * @since 1.0.0
 */

function sfw_dir( $path = '' ) : string {

  return sfw_plugin()->dir . $path;
}




/**
 * Alias of sfw_dir
 *
 * @see sfw_dir
 */

function sfw_url( $path = '' ) : string {

  return sfw_dir( $path );
}




/**
 * Retrieve the current plugin version.
 *
 * @return string plugin version
 * @since 1.0.0
 */

function sfw_version() : string {

  return sfw_plugin()->version;
}




/**
 * Checks if this is the pro version of this plugin
 *
 * @return bool
 * @since 1.0.0
 */

function sfw_is_pro( ) : bool {
  return sfw_plugin()->is_pro;
}




/**
 * Retrieve on of two values depending on plugin version
 *
 * @param  mixed $pro_val
 * @param  mixed $free_val
 *
 * @return mixed
 *
 * @since  1.0.0
 */

function sfw_pro_or( $pro_val, $free_val = '' ) {
  return sfw_is_pro() ? $pro_val : $free_val;
}




/**
 * Includes a file
 *
 * @param  string $path A file relative to the plugin root.
 * @param $data Optional. A set of variables that should be exposed to the included file.
 * @return mixed|void  If the included file returns a value, than this value is returned.
 * @since 1.0.0
 */

function sfw_include( $path, $data = array() ) {

  $file = sfw_path( $path );

  if( !empty( $data ) )
    extract( $data );

  return include_once( $file );
}




/**
 * Stores vars inside the main class.
 *
 * Use one parameter as getter, use two parameters as setter. This is done to reduce the use of globals.
 *
 * @param  string $key   A key that identifies the variable
 * @param  mixed $value   Any value
 * @return mixed  A previously set value or nothing.
 * @since 1.0.0
 */

function sfw_var( $key, $value = null ){

  if( is_null( $value ) ) {
    return isset( sfw_plugin()->storage[ $key ] ) ? sfw_plugin()->storage[ $key ] : null;
  }
  else {
    sfw_plugin()->storage[ $key ] = $value;
  }
}




/**
 * Save way to retrieve a constant that uses the plugins prefix.
 *
 * @param  string $key The constants name.
 * @return mixed  The value of the constant.
 * @since 1.0.0
 */

function sfw_constant( $key ) {
  $key = 'SFW_'.$key;
  return defined( $key ) ? constant( $key ) : null;
}




/**
 * Save way to define a constant that uses the plugins prefix.
 *
 * @param  string $key The constants name.
 * @param  mixed $value The value of the constant.
 * @return void
 * @since 1.0.0
 */

function sfw_define( $key, $value ) {
  $key = 'SFW_'.$key;
  if( !defined( $key ) )
    define( $key, $value );
}




/**
 * Retrieve the main capability used to control access to the plugins admin.
 *
 * @return string capability
 * @since 1.0.0
 */

function sfw_get_manage_cap() {

  /**
   * The main capability used to control access to the plugins admin.
   *
   * @var string A wordpress capability
   */
  $cap = apply_filters( 'sfw/manage_cap', 'manage_options' );

  return $cap;
}




/**
 * Checks if the current user is allowed to change shop options.
 *
 * @return boolean
 * @since 1.0.0
 */

function sfw_current_user_can_manage_sfw() : bool {

  // @todo
  return true;

  return current_user_can( sfw_get_manage_cap() );
}




/**
 * Check if $id is likely a Spreadshirt Id.
 *
 * Does not actually check if the $id is valid or exists.
 *
 * @param  string|int  $id [description]
 * @return boolean true if possible Spreadshirt Id
 * @since 1.0.0
 */

function maybe_is_spreadshirt_id( $id ) : bool {

  // 1 +
  if( is_numeric( $id ) && !empty( $id ) ) {
    return true;
  }
  // m12345-1
  elseif( is_string( $id ) && !empty( $id ) ) {
    return true;
  }

  return false;
}




/**
 * Forces single values to be wrapped in an array
 *
 * @param  mixed $var any value
 * @return array If $var was an array, returns $var, else returns array( $var )
 * @since 1.0.0
 */

function sfw_make_array( $var ) : array {

  return is_array( $var ) ? $var : array( $var );
}




/**
* Echo a string depending on $number
*
* Default is to work with 3 strings for zero, singular and plural. If you
* offer more than 3 strings, the last one is always used as plural, while
* every other is user by comparing it's numeric array index with $number
*
* @param int $number
* @param array $defaults
* @param array $strings
* @return string
* @since 1.0.0
*/

function sfw_numerus( $number, $defaults, $strings = array() ) {

  $strings = $strings + $defaults + array( 'no', 'one', '%s' );

  $number = intval( $number );

  $string = isset( $strings[ $number ] )
    ? $strings[ $number ]
    : array_pop( $strings );

  return sprintf( $string, $number );
}




/**
* Echo only if $value is not empty
*
* @param
* @since 1.0.0
*/

function sfw_conditional_echo( $value, $before = '', $after = '' ) {

  if( !empty( $value ) ) {

    echo $before.$value.$after;
  }
}




/**
* Retrieve node from Json Object
*
* @param array $objects - an array of objects
* @param string $property - a property name
* @param string|array $needle - a possible value or an array of values
* @param string $return - 'FIRST_RESULT' for a single result or 'ARRAY' for possible multiple results
*
* @return array|false|mixed  Returns the node or false if no node was found. Returns array depending on $return param
*
* @since 1.0.0
*/

function sfw_search_array_node( $objects, $property, $needle, $return = 'FIRST_RESULT' ) {

  $retval = array();
  $needle = sfw_make_array( $needle );

  foreach( $objects as $object ) {

    if( !property_exists( $object, $property ) ) continue;

    if( in_array( $object->{$property}, $needle ) ) {

      if( $return == 'FIRST_RESULT' )
        return $object;

      $retval[] = $object;
    }
  }

  return $return == 'FIRST_RESULT' ? false : $retval;
}



/**
 * Adds leading #
 *
 * @param  string $string
 * @return string
 * @since  1.0.0
 */

function sfw_leadinghashit( $string ) {
	return '#' . sfw_unleadinghashit( $string );
}


/**
 * Removes leading #
 *
 * @param  string $string
 * @return string
 * @since  1.0.0
 */

function sfw_unleadinghashit( $string ) {
	return ltrim( $string, '#' );
}