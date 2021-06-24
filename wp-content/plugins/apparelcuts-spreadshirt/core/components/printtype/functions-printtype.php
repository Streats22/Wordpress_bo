<?php if ( ! defined( '\ABSPATH' ) ) exit;





/**
 * Checks for printtype archive
 *
 * @return boolean
 *
 * @since 1.0.0
 */

function sfw_is_printtype_archive() {

  return is_tax('sfw-printtypid');
}




/**
 * Retrieve the current PrintType Object
 *
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 * @param  boolean $flush_cache
 * @param  boolean $transmit_errors
 *
 * @return Object | false
 *
 * @since 1.0.0
 */

function sfw_get_printtype( $printtype_selector = false, $flush_cache = false, $transmit_errors = false ){

  $spreadshirt_id = sfw_get_printtype_id( $printtype_selector );

  if( empty( $spreadshirt_id ) ) return false;

  return sfw_remote_get_cached( array(

    'url'     => sfw_create_shop_request_url( 'printTypes', $spreadshirt_id ),

    'query_args' => array(
      'locale' => sfw_get_locale(),
    ),

    'filter'  => 'printtype',

    'expire'  => WEEK_IN_SECONDS,

    'cache'   => SFW_Remote_Cache_Entity::get_instance( 'printtypeid', $spreadshirt_id ),

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));
}




/**
 * Retrieve the current printtype Term
 *
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 *
 * @return Object | false
 *
 * @since 1.0.0
 */

function sfw_get_printtype_term( $printtype_selector = false ){

  $printtype_id = sfw_get_printtype_id( $printtype_selector );

  $term = sfw_get_term( 'printtype', $printtype_id );

  return apply_filters('sfw/printtype/term', $term );

}




/**
 * Retrieve the ID of the current printtype in the Sfw Loop. This function tries
 * to get the ID from a less lasting source first. e.g. we don't have to load the whole printtype Data
 *
 * @param  WP_Term|WP_Post|int $printtype_selector
 *
 * @return false|int
 *
 * @since 1.0.0
 */

function sfw_get_printtype_id( $printtype_selector = false ) {

  if( maybe_is_spreadshirt_id( $printtype_selector ) ) {
    $printtype_id = $printtype_selector;
  }
  elseif( sfw_configurations_in_the_loop() ){
		$printtype_id = sfw_configuration_printtype_id();
  }
  else {

    $printtype_id = sfw_maybe_guess_entity_spreadshirt_id( 'printtype', $printtype_selector );
  }
  return $printtype_id ?: false;

}




/**
 * Echoes the printtype id
 *
 * @see sfw_get_printtype_id
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 * @since 1.0.0
 */

function sfw_printtype_id( $printtype_selector = false ) {

  echo sfw_get_printtype_id( $printtype_selector );
}




/**
 * Show printtype or not
 *
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_show_printtype( $printtype_selector = false ) : bool {

  $show = false;

  if( !empty( $printtype_term = sfw_get_printtype_term( $printtype_selector ) ) ) {

    $show = get_field('show_printtype_info', $printtype_term );
  }

  return apply_filters('sfw/printtype/show', $show );
}




/**
 * Retrieve the Name of the current printtype.
 * By default the Brandname will be stripped from the printtype Name.
 *
 * @see _sfw_hook_remove_brand_from_printtype_name
 *
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 * @return string | false
 *
 * @since 1.0.0
 */

function sfw_get_printtype_name( $printtype_selector = false ) {

  return !empty( $printtype = sfw_get_printtype( $printtype_selector ) )
      ? apply_filters('sfw/printtype/name', $printtype->name, $printtype )
      : false;
}




/**
 * Echoes the printtype name
 *
 * @see sfw_get_printtype_name
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 *
 * @since 1.0.0
 */

function sfw_printtype_name( $printtype_selector = false ) {

	echo sfw_get_printtype_name( $printtype_selector );
}




/**
 * Retrieve the Description of the current printtype.
 *
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 * @return string
 *
 * @since 1.0.0
 */

function sfw_get_printtype_description( $printtype_selector = false ) {

  $description = '';

  if( !empty( $printtype_term = sfw_get_printtype_term( $printtype_selector ) ) ) {

    $description = $printtype_term->description;

    if( empty( $description ) && !empty( $printtype = sfw_get_printtype() ) ) {

      $description = $printtype->description;
    }
  }

  return apply_filters('sfw/printtype/description', $description, $printtype_term );
}




/**
 * Echoes the printtype description
 *
 * @see sfw_get_printtype_description
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 *
 * @since 1.0.0
 */

function sfw_printtype_description( $printtype_selector = false ) {
	echo sfw_get_printtype_description( $printtype_selector );
}




/**
 * Retrieve the printtypes permalink
 *
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 * @return string
 *
 * @since 1.0.0
 */

function sfw_get_printtype_permalink( $printtype_selector = false ) {

  return !empty( $printtype_term = sfw_get_printtype_term( $printtype_selector ) )
    ? apply_filters('sfw/printtype/permalink', get_term_link( $printtype_term, 'sfw-printtype' ), $printtype_term )
    : '';
}




/**
 * Echoes the printtype permalink
 *
 * @see sfw_get_printtype_permalink
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 *
 * @since 1.0.0
 */

function sfw_printtype_permalink( $printtype_selector = false ) {
  echo sfw_get_printtype_permalink( $printtype_selector );
}




/**
 * Retrieve the number of articles which are associated to the current printtype
 *
 * the function will return 0 in case of an error
 *
 * @param  mixed $printtype_selector see sfw_get_printtype_id
 * @return int
 *
 * @since 1.0.0
 */

function sfw_get_printtype_articles_number( $printtype_selector = false ){

  return !empty( $printtype_term = sfw_get_printtype_term( $printtype_selector ) )
    ? apply_filters('sfw/printtype/articles_number', $printtype_term->count, $printtype_term )
    : 0;
}
