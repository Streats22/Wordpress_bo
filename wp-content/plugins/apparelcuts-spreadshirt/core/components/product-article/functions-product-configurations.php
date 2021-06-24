<?php if ( ! defined( '\ABSPATH' ) ) exit;





/**
 * Get all configurations
 *
 * @param  mixed $product_selector See sfw_get_product_id
 *
 * @return false|object
 *
 * @since 1.0.0
 */
function sfw_get_configurations( $product_selector = false ) {

  return !empty( $product = sfw_get_product( $product_selector ) )
    ? $product->configurations
    : false;
}




/**
 * Get a configuration Loop
 *
 * @uses Sfw_Node_Loop
 *
 * @param  mixed $product_selector See sfw_get_product_id
 *
 * @return [type]
 *
 * @since 1.0.0
 */

function sfw_get_configurations_loop( $product_selector = false ) {


  $product_id = sfw_get_product_id( $product_selector );

  if( empty( $product_id ) )
    return false;

  $found = false;
  $configuration_loop = wp_cache_get( $product_id, 'configuration-loop', false, $found );

  if( !$found ) {

    $product = sfw_get_product( $product_id );


    $configuration_loop = is_spreadshirt_object( $product )
      ? new Sfw_Node_Loop( $product->configurations )
      : false;

    wp_cache_set( $product_id, $configuration_loop,  'configuration-loop' );
  }

  return $configuration_loop;
}




/**
 * Iterate through configurations. Use it like have_posts
 *
 * @param  mixed $product_selector See sfw_get_product_id
 *
 * @return true | false
 *
 * @since 1.0.0
 */

function sfw_have_configurations( $product_selector = false ) {

  $product_id = sfw_get_product_id( $product_selector );

  if( empty( $configuration_loop = sfw_get_configurations_loop( $product_id ) ) )
    return false;


  $have = $configuration_loop->have_nodes();

  // the loop changed, so refresh cache
  wp_cache_set( $product_id, $configuration_loop,  'configuration-loop' );


  return $have;
}




/**
 * Check if the configuration Loop is runnig
 *
 * @param  mixed $product_selector See sfw_get_product_id
 *
 * @return true | false
 *
 * @since 1.0.0
 */

function sfw_configurations_in_the_loop( $product_selector = false ) {

  return !empty( $configuration_loop = sfw_get_configurations_loop( $product_selector ) )
    ? $configuration_loop->in_the_loop()
    : false;
}




/**
 * Retrieve the configuration
 *
 * @param  mixed $product_selector See sfw_get_product_id
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_current_configuration( $product_selector = false ) {

  $configuration_loop = sfw_get_configurations_loop( $product_selector );

  return $configuration_loop->in_the_loop()
    ? $configuration_loop->current_node()
    : false;
}




/**
 * Retrieve the configuration id
 *
 * @param  mixed $product_selector See sfw_get_product_id
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_current_configuration_id( $product_selector = false ) {

  $configuration_loop = sfw_get_current_configuration( $product_selector );

  return $configuration_loop
    ? $configuration_loop->id
    : false;
}




/**
 * Get either the configuration or a specified configuration of the Product
 *
 * @param  string $configuration_id
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_configuration( $configuration_id = false ) {

  if( empty( $configuration_id ) ) {

    return sfw_get_current_configuration();
  }

  return sfw_search_array_node( sfw_get_configurations(), 'id', $configuration_id );
}




/**
 * Retrieve the Id of the configuration
 *
 * @param  string $configuration_id
 *
 * @return false|int
 *
 * @since 1.0.0
 */

function sfw_get_configuration_id( $configuration_id = false ) {

  return !empty( $configuration = sfw_get_configuration( $configuration_id ) )
    ? (int)$configuration->id
    : false;
}




/**
 * Echoes the Id of the configuration
 *
 * @since 1.0.0
 */

function sfw_configuration_id() {

  echo sfw_get_configuration_id();
}




/**
 * Retrieve the type of the configuration
 *
 * @param  string $configuration_id
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_configuration_type( $configuration_id = false ) {

  return !empty( $configuration = sfw_get_configuration( $configuration_id ) )
    ? apply_filters('sfw/configuration/type', (string) $configuration->type )
    : false;
}




/**
 * Echoes the type of the configuration
 *
 * @param  string $configuration_id
 *
 * @since 1.0.0
 */

function sfw_configuration_type( $configuration_id = false ) {

  echo sfw_get_configuration_type( $configuration_id );
}




/**
 * Test the Type of the configuration
 *
 * @param  string  $configuration_type
 * @param  string $configuration_id
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_is_configuration_type( $configuration_type, $configuration_id = false ) : bool {

  return sfw_get_configuration_type( $configuration_id ) == $configuration_type;
}




/**
 * If the configuration is text
 *
 * @param  string $configuration_id
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_is_text_configuration( $configuration_id = false ) : bool {

  return sfw_get_configuration_type( $configuration_id ) == 'text';
}




/**
 * If the configuration is design
 *
 * @param  string $configuration_id
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_is_design_configuration( $configuration_id = false ) : bool {

  return sfw_get_configuration_type( $configuration_id ) == 'design';
}




/**
 * Retrieve the PrintType Id of the configuration
 *
 * @param  string $configuration_id
 *
 * @return false|int
 *
 * @since 1.0.0
 */

function sfw_get_configuration_printtype_id( $configuration_id = false ) {

  return !empty( $configuration = sfw_get_configuration( $configuration_id ) )
    ? apply_filters('sfw/configuration/printtype/id', (string) $configuration->printType->id )
    : false;
}




/**
 * Echoes the PrintType Id of the configuration
 *
 * @param  string $configuration_id
 *
 * @return int
 *
 * @since 1.0.0
 */

function sfw_configuration_printtype_id( $configuration_id = false ) {

  echo sfw_get_configuration_printtype_id( $configuration_id );
}




/**
 * Retrieve the Design Id of the configuration
 *
 * @param  string $configuration_id
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_configuration_design_id( $configuration_id = false ) {

  return sfw_is_design_configuration( $configuration_id ) && !empty( $configuration = sfw_get_configuration( $configuration_id ) )
    ? apply_filters('sfw/configuration/design/id', (string) $configuration->designs[0]->id )
    : false;
}




/**
 * Echoes the Design Id of the configuration
 *
 * @param  string $configuration_id
 *
 * @return [type]
 *
 * @since 1.0.0
 */

function sfw_configuration_design_id( $configuration_id = false ) {

  echo sfw_get_configuration_design_id( $configuration_id );
}




/**
 * Retrive array with all of a products design ids
 *
 * @param  mixed $product_selector See sfw_get_product_id
 *
 * @return array
 *
 * @since 1.0.0
 */

function sfw_product_get_all_design_ids( $product_selector = false ) {

  $design_ids = array();

  while( sfw_have_configurations( $product_selector ) ) {

    $configuration =  sfw_get_current_configuration( $product_selector );

    if( $configuration->type == 'design' ) {

      foreach( $configuration->designs as $design ) {

        $design_ids[] = $design->id;

      }

    }

  }

  return array_unique( $design_ids );

}




/**
 * Retrive array with all of a products printtype ids
 *
 * @param  mixed $product_selector See sfw_get_product_id
 *
 * @return array
 *
 * @since 1.0.0
 */

function sfw_product_get_all_printtype_ids( $product_selector = false ) {

  $printtype_ids = array();

  while( sfw_have_configurations( $product_selector ) ) {

    $configuration =  sfw_get_current_configuration( $product_selector );

    $printtype_ids[] = $configuration->printType->id;
  }

  return array_unique( $printtype_ids );

}
