<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
 * Helper for short images args
 *
 * Allows for array( 400, 300 ) instead of array( 'width' => 400, 'height' => 300 )
 *
 * @param  array $args
 * @return array
 * @since  1.0.0
 */

function sfw_parse_short_circuit_image_args( $args ) {

	// allow shorthand notation "array( 100, 100 )" as settings
	if( count( $args ) == 2 && isset($args[0]) && isset($args[1]) && is_numeric($args[0]) && is_numeric($args[1])) {
		$args = array(
			'width'  => $args[0],
			'height' => $args[1]
		);
	}

  return $args;
}




/**
 * Parses image args
 *
 * @param  array $args
 * @param  array $defaults
 * @return array
 * @since  1.0.0
 */

function sfw_parse_image_args( $args, $defaults ) {

  $args = sfw_parse_short_circuit_image_args( $args );
  $args = wp_parse_args( $args, $defaults );
  $args = wp_parse_argS( $args, array(
    'query' => array(),
    'parts' => array(),
  ));

  return $args;
}




/**
* Retieve the design image object
*
* @param array $args arguments for creating the object, see spreadshirt_image()
* @param mixed $selector A design selector
* @return false|object
* @see spreadshirt_image()
* @since 1.0.0
*/

function sfw_design_image( $args = array(), $selector = false ) {

  if( empty( $design_id = sfw_get_design_id( $selector ) ) )
    return;

  $defaults = array(
    'parts'      => array( 'designs', $design_id ),
    'name'       => sfw_get_design_name( $design_id ),
    'background' => sfw_get_design_background_color( false, $design_id ),
  );

  $args = sfw_parse_image_args( $args, $defaults );

  return spreadshirt_image( $args );
}




/**
* Retieve the product/article image object
*
* @param array $args arguments for creating the object, see spreadshirt_image()
* @param mixed $selector An article selector
* @return false|object
* @see spreadshirt_image()
* @since 1.0.0
*/

function sfw_article_image( $args = array(), $selector = false ) {

  if( empty( $article = sfw_get_article( $selector ) ) )
    return;

  // view
  if( !$view = @$args['view'] ) {
    if( sfw_views_in_the_loop() ) {
    	$view = sfw_get_view_id();
    }
    else {
      $view = sfw_get_article_view( $article );
    }
  }


  // appearance
  if( !$appearance = @$args['appearance'] ) {
    $appearance = sfw_get_article_appearance_id();
  }


  $defaults = array(
    'parts'      => array( 'products', $article->product->id, 'views', $view ),
    'name'       => sfw_get_article_name_fallback( $article->id )
  );

  $args = sfw_parse_image_args( $args, $defaults );


  $args['query']['appearanceId'] = $appearance;

  return spreadshirt_image( $args );
}




/**
* Retieve the composition image object.
*
* @param array $args arguments for creating the object, see spreadshirt_image()
* @param mixed $selector An article selector
* @return false|object
* @see spreadshirt_image()
* @since 1.0.0
*/

function sfw_composition_image( $args = array(), $selector = false ) {

  if( empty( $article = sfw_get_article( $selector ) ) )
    return;

  $defaults = array(
    'parts'      => array( 'compositions', $article->product->id, 'views', $view )
  );

  $args = sfw_parse_image_args( $args, $defaults );

  return sfw_create_article_image( $args, $selector );
}




/**
* Retieve the ProductType image object.
*
* @param array $args arguments for creating the object, see spreadshirt_image()
* @param mixed $selector A producttype selector
* @return false|object
* @see spreadshirt_image()
* @since 1.0.0
*/

function sfw_producttype_image( $args = array(), $selector = false ) {

  if( empty( $producttype = sfw_get_producttype( $selector ) ) )
    return;

  // view
  if( !$view = @$args['view'] ) {
    if( sfw_views_in_the_loop() ) {
    	$view = sfw_get_view_id();
    }
    else {
      $view = 1;
    }
  }


  // appearance
  if( !$appearance = @$args['appearance'] )
    $appearance = sfw_get_appearance_id();


  $defaults = array(
    'parts'      => array( 'productTypes', $producttype->id, 'views', $view ),
    'name'       => sfw_get_producttype_name( $producttype->id )
  );

  $args = sfw_parse_image_args( $args, $defaults );

	if( $appearance )
  	$args['query']['appearanceId'] = $appearance;

  return spreadshirt_image( $args );
}




/**
* Retieve the ProductType Detail image object.
*
* @param array $args arguments for creating the object, see spreadshirt_image()
* @param mixed $selector A producttype selector
* @return false|object
* @see spreadshirt_image()
* @since 1.0.0
*/

function sfw_producttype_detail_image( $args = array(), $selector = false ) {

  if( empty( $producttype = sfw_get_producttype( $selector ) ) )
    return;


  $defaults = array(
    'parts'      => array( 'productTypes', $producttype->id, 'variants', 'detail' ),
    'name'       => sfw_get_producttype_name( $producttype->id )
  );

  $args = sfw_parse_image_args( $args, $defaults );


  return spreadshirt_image( $args );
}





/**
* Retieve the ProductType Size image object.
*
* @param array $args arguments for creating the object, see spreadshirt_image()
* @param mixed $selector A producttype selector
* @return false|object
* @see spreadshirt_image()
* @since 1.0.0
*/

function sfw_producttype_size_image( $args = array(), $selector = false ) {

  if( empty( $producttype = sfw_get_producttype( $selector ) ) )
    return;


  $defaults = array(
    'parts'      => array( 'productTypes', $producttype->id, 'variants', 'size' ),
    'name'       => sfw_get_producttype_name( $producttype->id )
  );

  $args = sfw_parse_image_args( $args, $defaults );


  return spreadshirt_image( $args );
}




/**
* Retrieve the Appearance image object.
*
* @param array $args arguments for creating the object, see spreadshirt_image()
* @param int $appearance Optional. An appearance id.
* @return false|object
* @see spreadshirt_image()
* @since 1.0.0
*/

function sfw_appearance_image( $args = array(), $appearance = false ) {

  if( !$appearance ) {
    $appearance = sfw_get_appearance_id();
  }

  $defaults = array(
    'parts'      => array( 'appearances', $appearance ),
  );

  $args = sfw_parse_image_args( $args, $defaults );


  return spreadshirt_image( $args );
}




/**
* Retieve a Configuration image object.
*
* @param array $args arguments for creating the object, see spreadshirt_image()
* @param int $configuration_id Optional. A configuration id.
* @return false|object
* @see spreadshirt_image()
* @since 1.0.0
*/
function sfw_configuration_image( $args = array(), $configuration_id = false ) {

  if( !$configuration_id ) {
    $configuration_id = sfw_get_configuration_id();
  }

  // appearance
  if( !$appearance = @$args['appearance'] ) {
    $appearance = sfw_get_article_appearance_id();
  }

  $defaults = array(
    'parts'      => array( 'configurations', $configuration_id ),
  );

  $args = sfw_parse_image_args( $args, $defaults );

  $args['query']['appearanceId'] = $appearance;

  return spreadshirt_image( $args );
}


