<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
* This File deals mainly with the JSON response for the productType resource,
* and Terms of the 'sfw-producttype' Taxonomy
*
*/




/**
 * Retrieve Producttypes list
 *
 * Some data is stripped, so it fits the database
 *
 * @param  boolean $flush_cache
 * @param  boolean $transmit_errors
 * @return
 * @since  1.0.0
 */

function sfw_get_producttypes_minified( $flush_cache = false, $transmit_errors = false ){

  return sfw_remote_get_cached( array(

    'url'      => sfw_create_shop_request_url( 'productTypes' ),

    'filter'   => 'producttypes_minified',

    'cache'    => null,

    'flush'   => $flush_cache,

    'transmit_errors' => $transmit_errors,

    'query_args' => array(
      'fullData' => 'true',
      'limit' => 500,
    )

  ));

}




/**
 * Minify producttypes payload
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_minify_producttypes( $response ) {

  try {

    array_walk( $response->productTypes, function( &$producttype ) {
      unset( $producttype->stockStates );
      unset( $producttype->created );
      unset( $producttype->modified );
      unset( $producttype->lifeCycleState );
      unset( $producttype->shortDescription );
      unset( $producttype->description );
      unset( $producttype->categoryName );
      unset( $producttype->discountSupported );
      unset( $producttype->separatePrintouts );
      unset( $producttype->customsTariffCode );
      unset( $producttype->manufacturingCountry );
      unset( $producttype->href );
      unset( $producttype->printAreas );
      unset( $producttype->washingInstructions );
      unset( $producttype->attributes );
      unset( $producttype->giftWrappingSupported );
      unset( $producttype->handler );
      unset( $producttype->version );
      unset( $producttype->price->currency->href );
      unset( $producttype->resources );

      foreach( $producttype->appearances as &$appearance ) {
        unset( $appearance->printTypes );
      }

      foreach( $producttype->views as &$view ) {
        unset( $view->size );
        unset( $view->viewMaps );
        unset( $view->montage );
        unset( $view->resources );
        unset( $view->state );
      }

    });

  } catch (\Exception $e) {

  }

  return $response;

}

add_filter( 'sfw/producttypes_minified/response', '_sfw_hook_minify_producttypes' );




/**
 * Check for producttype taxonomy archive
 *
 * @return boolean
 *
 * @since 1.0.0
 */

function sfw_is_producttype_archive() : bool {

  return is_tax('sfw-producttype');
}




/**
 * Retrieve the producttype Object
 *
 * @uses sfw_get_producttype_id()
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 * @param  boolean $flush_cache
 * @param  boolean $transmit_errors
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_producttype( $producttype_selector = false, $flush_cache = false, $transmit_errors = false ){

  // get id
  $spreadshirt_id = sfw_get_producttype_id( $producttype_selector );

  if( empty( $spreadshirt_id ) ) return false;

  return sfw_remote_get_cached( array(

    'url'    => sfw_create_shop_request_url( 'productTypes', $spreadshirt_id ),

    'query_args' => array(
      'fullData' => 'true',
      'locale' => sfw_get_locale(),
    ),

    'filter' => 'producttype',

    'expire' => 3 * DAY_IN_SECONDS,

    'cache'  => SFW_Remote_Cache_Entity::get_instance( 'producttype', $spreadshirt_id ),

    'flush'  => $flush_cache,

    'transmit_errors' => $transmit_errors

  ));
}




/**
 * Retrieve the producttype Term
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_producttype_term( $producttype_id = false ){

  $producttype_id = sfw_get_producttype_id( $producttype_id );

  return !empty( $term = sfw_get_item( 'producttype', $producttype_id ) )
    ? apply_filters('sfw/producttype/term', $term )
    : false;
}




/**
 * Retrieve all ProductType Terms
 *
 * @return array of $term Objects | false
 *
 * @since 1.0.0
 */

function sfw_get_producttype_terms() {

  return sfw_get_all_terms( 'sfw-producttype', 'spreadshirt-id' );
}




/**
 * Retrieve the ID of the producttype in the Sfw Loop. This function tries
 * to get the ID from a less lasting source first. e.g. we don't have to load the whole ProductType Data
 *
 * @param  mixed $producttype_selector can be an actual ProductType Id, WP_Term ( sfw-producttype ) or empty ( auto guessing )
 *
 * @return false|int
 *
 * @since 1.0.0
 */

function sfw_get_producttype_id( $producttype_selector = false ) {

  return sfw_maybe_guess_entity_spreadshirt_id( 'producttype', $producttype_selector );
}




/**
 * Retrieve the producttype id
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_producttype_id( $producttype_selector = false ) {

  echo sfw_get_producttype_id( $producttype_selector );
}




/**
 * Retrieve the Name of the producttype.
 *
 * By default the Brandname will be stripped from the Producttype Name.
 *
 * @see _sfw_hook_remove_brand_from_producttype_name
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return string|false
 *
 * @since 1.0.0
 */

function sfw_get_producttype_name( $producttype_selector = false ) {

  return !empty( $producttype = sfw_get_producttype( $producttype_selector ) )
      ? apply_filters('sfw/producttype/name', $producttype->name, $producttype )
      : false;
}




/**
 * Echoes the name of the producttype.
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_producttype_name( $producttype_selector = false ) {

	echo sfw_get_producttype_name( $producttype_selector );
}




/**
 * Retrieve the ShortDescription of the producttype.
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return string|false
 *
 * @since 1.0.0
 */

function sfw_get_producttype_short_description( $producttype_selector = false ) {

  return !empty( $producttype = sfw_get_producttype( $producttype_selector ) )
      ? apply_filters('sfw/producttype/shortdescription', $producttype->shortDescription, $producttype )
      : false;
}





/**
 * Echoes the ShortDescription of the producttype.
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_producttype_short_description( $producttype_selector = false ) {

	echo sfw_get_producttype_short_description( $producttype_selector );
}




/**
 * Retrieve the Description of the producttype.
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return string|false
 *
 * @since 1.0.0
 */

function sfw_get_producttype_description( $producttype_selector = false ) {

  return !empty( $producttype = sfw_get_producttype( $producttype_selector ) )
      ? apply_filters('sfw/producttype/description', $producttype->description, $producttype )
      : false;
}




/**
 * Echoes the Description of the producttype.
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_producttype_description( $producttype_selector = false ) {

	echo sfw_get_producttype_description( $producttype_selector);
}




/**
 * Return all Appearance Nodes
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_producttype_appearances( $producttype_selector = false ) {

  return !empty( $producttype = sfw_get_producttype( $producttype_selector ) )
    ? apply_filters( 'sfw/producttype/appearances', $producttype->appearances )
    : false;
}




/**
 * Search for Appearance with $appearance_id in ProductType Appearances
 *
 * @param  string  $appearance_id a valid appearance_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return object|false
 *
 * @since 1.0.0
 */

function sfw_get_producttype_appearance( $appearance_id, $producttype_selector = false ) {

  $appearances = sfw_get_producttype_appearances( $producttype_selector );

  return !empty( $appearances )
    ? sfw_search_array_node( $appearances, 'id', $appearance_id )
    : false;
}




/**
 * Search for Appearance Name with $appearance_id in ProductType Appearances
 *
 * @param  string  $appearance_id a valid appearance_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return string|false
 *
 * @since 1.0.0
 */

function sfw_get_producttype_appearance_name( $appearance_id, $producttype_selector = false  ) {

  return !empty( $appearance = sfw_get_producttype_appearance( $appearance_id,  $producttype_selector ) )
    ? apply_filters('sfw/appearance/name', $appearance->name, $appearance )
    : false;
}




/**
 * Retrieve the producttypes permalink
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return string
 *
 * @since 1.0.0
 */

function sfw_get_producttype_permalink( $producttype_selector = false  ) {

  return !empty( $producttype_term = sfw_get_producttype_term( $producttype_selector ) )
    ? apply_filters('sfw/producttype/permalink', get_term_link( $producttype_term, 'sfw-producttype' ), $producttype_term )
    : false;
}




/**
 * Echoes the producttypes permalink
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_producttype_permalink( $producttype_selector = false ) {

  echo sfw_get_producttype_permalink( $producttype_selector );
}




/**
 * Retrieve the number of articles which are associated to the current producttype
 *
 * The function will return 0 in case of an error
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return int
 *
 * @since 1.0.0
 */

function sfw_get_producttype_articles_number( $producttype_selector = false  ){

  return !empty( $producttype_term = sfw_get_producttype_term( $producttype_selector ) )
    ? apply_filters('sfw/producttype/articles_number', $producttype_term->count, $producttype_term )
    : 0;
}




/**
 * Echoes the labeled number of articles which are associated to the current producttype
 *
 * @param  string $zero
 * @param  string $one
 * @param  string $more
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_producttype_articles_number( $zero = false, $one = false, $more = false, $producttype_selector = false  ){

  $num = sfw_get_producttype_articles_number( $producttype_selector );

  if( 0 === $num ) {
    if( false === $zero ){
      echo _x('No Articles', 'Articles Count Zero', 'apparelcuts-spreadshirt' );
    }
    else {
      echo $zero;
    }
  }
  elseif( 1 === $num ) {
    if( false === $one ){
      echo _x('One Article', 'Articles Count Singular/One', 'apparelcuts-spreadshirt' );
    }
    else {
      echo $one;
    }
  }
  elseif( $num > 1 ) {
    if( false === $more ){
      printf( _x('%d Articles', 'Articles Count Plural', 'apparelcuts-spreadshirt' ), $num );
    }
    else {
      echo $more;
    }
  }
}
