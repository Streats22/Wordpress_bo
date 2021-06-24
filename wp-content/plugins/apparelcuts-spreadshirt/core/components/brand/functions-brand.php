<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
* This File deals mainly with the Brand Node of the productType resource,
* and Terms of the 'sfw-brand' Taxonomy
*/


/**
 * Create new Brand Term
 *
 * @param string $brandname
 *
 * @return WP_Term|WP_Error
 *
 * @since 1.0.0
 */

function sfw_create_brand( $brandname ) {

	$spreadshirt_id = $brandname;
	$brandname      = apply_filters( 'sfw/brand/create/name', $brandname );

  // double check
  if( sfw_item_exists( 'brand', $spreadshirt_id ) )
    return sfw_create_error( 'creation', __('Brand already exists.', 'apparelcuts-spreadshirt' ), $spreadshirt_id );

  // insert
	$_termcfg = wp_insert_term( $brandname, sfw_get_entity_taxonomy( 'brand' ) );

  // bail
  if( is_wp_error( $_termcfg ) )
    return sfw_pass_error( $_termcfg );


	add_term_meta(
    $_termcfg['term_id'],
    sfw_get_entity_wp_metakey( 'brand' ),
    $spreadshirt_id
  );

  return get_term( $_termcfg['term_id'] );
}




/**
 * Retrieve the current Brand Term
 *
 * @return WP_Term|false
 *
 * @since 1.0.0
 */

function sfw_get_brand(){

  return !empty( $brand = sfw_get_primary_term( sfw_get_entity_taxonomy( 'brand' ) ) )
    ? $brand
    : false;
}




/**
 * Retrieve the current brand name
 *
 * @return string|false The brands name
 *
 * @since 1.0.0
 */

function sfw_get_brand_name() {

  return !empty( $producttype = sfw_get_producttype() )
    ? apply_filters('sfw/brand/name', $producttype->brand, $producttype )
   : false;
}




/**
 * Echoes the brand name
 *
 * @since 1.0.0
 */

function sfw_brand_name( $before = '', $after = '' ) {
  $name = sfw_get_brand_name();

	if( $name ) echo $before, $name, $after;
}




/**
 * Retrieve the Name including important Brand Names of the current ProductType.
 *
 * @return string|false
 *
 * @since 1.0.0
 */

function sfw_get_branded_producttype_name() {

  $producttype_name = sfw_get_producttype_name();
  $brand_name       = sfw_get_brand_name();

  // should we show the brand?
  if( sfw_show_brand() ) {

    $producttype_name = sprintf( _x( '%s by %s', '%ProductTypeName by %Brand', 'apparelcuts-spreadshirt' ), $producttype_name, $brand_name );
  }

  return !empty( $producttype = sfw_get_producttype() )
      ? apply_filters('sfw/producttype/brandedname', $producttype_name, $producttype )
      : false;
}




/**
 * Echo the branded producttype name
 *
 * @see sfw_get_branded_producttype_name
 * @since 1.0.0
 */

function sfw_branded_producttype_name() {

  echo sfw_get_branded_producttype_name();
}




/**
 * Whether or not to show the current brand
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_show_brand( ) : bool {

  return apply_filters('sfw/brand/show', true, sfw_get_brand_name() );
}




/**
 * Hide some Brands by default
 *
 * @ignore
 * @since 1.0.0
 */

function _sfw_hook_hide_brands( $retval, $brand_name ) {

 // don't show brands that are already hidden
 if( $retval ) {

   $hidden_brands = array(
     _x('Spreadshirt', 'Brandname', 'apparelcuts-spreadshirt' )
   );

   $retval = !in_array( $brand_name, $hidden_brands );
 }

 return $retval;
}

add_filter('sfw/brand/show', '_sfw_hook_hide_brands', 1, 2 );




/**
 * Retrieve the current brand description
 *
 * @return string|false
 *
 * @since 1.0.0
 */

function sfw_get_brand_description() {

  return !empty( $brand = sfw_get_brand() )
    ? apply_filters( 'sfw/brand/description', $brand->description, $brand )
   : false;
}




/**
 * Echoes the current brand description
 *
 * @see sfw_get_brand_description
 * @since 1.0.0
 */

function sfw_brand_description() {
  echo sfw_get_brand_description();
}




/**
 * Spreadshirt attaches the Brandname automatically to some of the Producttype Names. To get more Control on this, we generally strip it off
 *
 * @hook 'sfw/producttype/name'
 * @see sfw_get_producttype_name
 * @ignore
 * @since 1.0.0
 */

function _sfw_hook_remove_brand_from_producttype_name( $producttype_name, $producttype ) {

	$brand = $producttype->brand;

  // if the brand is part of the producttype_name
	if( strpos( $producttype_name, $brand ) !== false ) {

		$replaces = array(
			', '.$brand,
			' Marke: '.$brand,
			' Brand: '.$brand,
			' von '.$brand,
			' by '.$brand,
			' van '.$brand,
			' fra '.$brand,
		);

    // just replace Brand including prefix
		foreach ($replaces as $replace ) {
			$producttype_name = str_replace( $replace, '', $producttype_name );
		}
	}

	return $producttype_name;
}

add_filter( 'sfw/producttype/name', '_sfw_hook_remove_brand_from_producttype_name', 10, 2 );




