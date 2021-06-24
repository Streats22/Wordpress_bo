<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
 * Registers an Entity
 *
 * @see SFW_Entity
 * @see SFW_Entity_Controller
 *
 * @param  string $name A unique Entity name
 * @param  array $properties
 *
 * @return WP_Error|SFW_Entity Returns new Entity on Success
 *
 * @since 1.0.0
 */

function sfw_register_entity( $name, $properties ) {

  new SFW_Entity( $name, $properties );
}



/**
 * Retrieve an Entity
 *
 * @see SFW_Entity_Controller
 *
 * @param  string|SFW_Entity $mixed An Entity name or Entity Instance
 *
 * @return WP_Error|SFW_Entity
 *
 * @since 1.0.0
 */

function sfw_get_entity( $mixed ){

  return SFW_Entity_Controller::get( $mixed );
}


/**
 * Retrieve multiple Entities
 *
 * @see SFW_Entity_Controller
 *
 * @param  array $args Arguments to filter Entities
 *
 * @return array An array of Entities. Empty array if no matches were found
 *
 * @since 1.0.0
 */

function sfw_get_entities( $args ){

  return SFW_Entity_Controller::get_entities( $args );
}




/**
 * Try to get an Entity by args
 *
 * @see sfw_get_entities
 *
 * @param  array $args
 *
 * @return false|SFW_Entity
 *
 * @since 1.0.0
 */

function sfw_maybe_get_entity( $args ) {

  $entities = sfw_get_entities( $args );

  return is_array( $entities ) && !empty( $entities )
    ? array_values( $entities )[0]
    : false;
}




/**
 * Check if an Entity exists
 *
 * @see SFW_Entity_Controller
 *
 * @param  string|SFW_Entity $mixed An Entity name or Entity Instance
 *
 * @return boolean
 *
 * @since 1.0.0
 */

function sfw_is_entity( $mixed ){

  return SFW_Entity_Controller::exists( $mixed );

}




/**
 * Retrieve a Post or Term for given Entity and Spreadshirt ID
 *
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 * @param  string $spreadshirt_id
 *
 * @return object|false Return Wordpress resource or false if none exists
 *
 * @since 1.0.0
 */

function sfw_get_item( $mixed_entity, $spreadshirt_id ) {

  $entity = sfw_get_entity( $mixed_entity );

  return sfw_is_entity( $entity )
    ? $entity->get_wp_item( $spreadshirt_id )
    : false;
}




/**
 * Retrieve WP_Post for given Entity and Spreadshirt ID
 *
 * @see sfw_get_item
 *
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 * @param  string $spreadshirt_id
 *
 * @return WP_Post|false Return WP_Post or false if none exists
 *
 * @since 1.0.0
 */

function sfw_get_post( $mixed_entity, $spreadshirt_id ) {

  $post = sfw_get_item( $mixed_entity, $spreadshirt_id );

  return sfw_is_wp_post( $post ) ? $post : false;
}




/**
 * Retrieve WP_Term for given Entity and Spreadshirt ID
 *
 * @see sfw_get_item
 *
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 * @param  string $spreadshirt_id
 *
 * @return WP_Term|false Return WP_Term or false if none exists
 *
 * @since 1.0.0
 */

function sfw_get_term( $mixed_entity, $spreadshirt_id ) {

  $term = sfw_get_item( $mixed_entity, $spreadshirt_id );

  return sfw_is_wp_term( $term ) ? $term : false;
}




/**
 * Checks if a Wordpress resource for given Entity and Spreadshirt ID exists
 *
 * @see sfw_get_item
 *
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 * @param  string $spreadshirt_id
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_item_exists( $mixed_entity, $spreadshirt_id ) : bool {

  $item = sfw_get_item( $mixed_entity, $spreadshirt_id );

  return  !is_wp_error( $item ) && !empty( $item );
}




/**
 * Checks if an item is valid for given Entity
 *
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 * @param  WP_Post|WP_Term $item
 *
 * @return boolean
 *
 * @since 1.0.0
 */

function sfw_is_valid_item( $mixed_entity, $item ) : bool {

  $entity = sfw_get_entity( $mixed_entity );

  return sfw_is_entity( $entity )
    ? $entity->is_valid_item( $item )
    : false;
}




/**
 * Triggers creation of new Entity Item
 *
 * @see SFW_Entity
 *
 * @param string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 * @param string $spreadshirt_id
 *
 * @return WP_Error|object
 *
 * @since 1.0.0
 */

function sfw_create_item( $mixed_entity, $spreadshirt_id ) {

  $entity = sfw_get_entity( $mixed_entity );

  return is_wp_error( $entity )
    ? $entity
    : $entity->create( $spreadshirt_id );
}




/**
 * Triggers update of new Entity Item
 *
 * @see SFW_Entity
 *
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 * @param  string $spreadshirt_id
 *
 * @return WP_Error|object
 *
 * @since 1.0.0
 */

function sfw_update_item( $mixed_entity, $spreadshirt_id ) {

  $entity = sfw_get_entity( $mixed_entity );

  return is_wp_error( $entity )
    ? $entity
    : $entity->update( $spreadshirt_id );
}




/**
 * Alias of sfw_get_item
 *
 * @see sfw_get_item
 *
 * @since 1.0.0
 */

function sfw_get_entity_post( $mixed_entity, $spreadshirt_id ) {
  return sfw_get_item( $mixed_entity, $spreadshirt_id );
}




/**
 * Alias of sfw_get_item
 *
 * @see sfw_get_item
 *
 * @since 1.0.0
 */

function sfw_get_entity_term( $mixed_entity, $spreadshirt_id ) {
  return sfw_get_item( $mixed_entity, $spreadshirt_id );
}




/**
 * Returns property from SFW_Entity instance
 *
 * @param  string $property_name
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 *
 * @return mixed the value of the Property or false if it does not exists
 *
 * @since 1.0.0
 */

function sfw_get_entity_property( $property_name, $mixed_entity ) {

  $entity = sfw_get_entity( $mixed_entity );

  return sfw_is_entity( $entity ) && property_exists( $entity, $property_name )
    ? $entity->$property_name
    : false;
}




/**
 * Retrieve Entitys wp_type property
 *
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 *
 * @return mixed the value of the Property or false if it does not exists
 *
 * @since 1.0.0
 */

function sfw_get_entity_wp_type( $mixed_entity ) {

  return sfw_get_entity_property( 'wp_type', $mixed_entity );
}




/**
 * Retrieve Entitys wp_subtype property
 *
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 *
 * @return mixed the value of the Property or false if it does not exists
 *
 * @since 1.0.0
 */

function sfw_get_entity_wp_subtype( $mixed_entity ) {

  return sfw_get_entity_property( 'wp_subtype', $mixed_entity );
}




/**
 * Alias of sfw_get_entity_posttype
 *
 * @see sfw_get_entity_posttype
 *
 * @since 1.0.0
 */

function sfw_get_entity_posttype( $mixed_entity ) {

  return sfw_get_entity_wp_subtype( $mixed_entity );
}




/**
 * Alias of sfw_get_entity_posttype
 *
 * @see sfw_get_entity_posttype
 *
 * @since 1.0.0
 */

function sfw_get_entity_taxonomy( $mixed_entity ) {

  return sfw_get_entity_wp_subtype( $mixed_entity );
}




/**
 * Retrieve Entitys wp_metakey property
 *
 * @param  string|SFW_Entity $mixed_entity An Entity name or Entity Instance
 *
 * @return mixed the value of the Property or false if it does not exists
 *
 * @since 1.0.0
 */

function sfw_get_entity_wp_metakey( $mixed_entity ) {

  return sfw_get_entity_property( 'wp_metakey', $mixed_entity );
}




/**
 * Retrieve an Entity by a given WP Resource like a post or term
 *
 * @param  mixed an Object that is associated with a Wordpress resource
 *               like a post or term
 *
 * @return false|SFW_Entity
 *
 * @since 1.0.0
 */

function sfw_get_wpobject_entity( $wpobj ) {

  if( $wpobj instanceof WP_Term )
    return sfw_get_entity_by_taxonomy( $wpobj->taxonomy );

  elseif( $wpobj instanceof WP_Post )
    return sfw_get_entity_by_posttype( $wpobj->post_type );

  return false;
}



/**
 * Retrieve an Entity by a given Taxonomy
 *
 * @param  string $taxonomy
 *
 * @return false|SFW_Entity
 *
 * @since 1.0.0
 */

function sfw_get_entity_by_taxonomy( $taxonomy ) {

  $args = array(
    'wp_type' => 'term',
    'wp_subtype' => $taxonomy
  );

  return sfw_maybe_get_entity( $args );
}




/**
 * Retrieve an Entity by a given posttype
 *
 * @param  string $posttype
 *
 * @return false|SFW_Entity
 *
 * @since 1.0.0
 */

function sfw_get_entity_by_posttype( $posttype ) {

  $args = array(
    'wp_type' => 'post',
    'wp_subtype' => $posttype
  );

  return sfw_maybe_get_entity( $args );
}




/**
 * Retrieve a Spreadshirt Id for a given Wordpress resource
 *
 * @param  object $wpobj A post or term that is registered as Entity
 *
 * @return string|false Returns the Spreadshirt Id if found, else false
 *
 * @since 1.0.0
 */

function sfw_get_spreadshirt_id_by_wpobj( $wpobj ) {

  $entity = sfw_get_wpobject_entity( $wpobj );

  return false !== $entity
    ? $entity->get_spreadshirt_id( $wpobj )
    : false;
}




/**
 * Retrieve a Spreadshirt Id for a given Term
 *
 * @see sfw_get_spreadshirt_id_by_wpobj
 *
 * @param  WP_Term $term
 *
 * @return false|SFW_Entity
 *
 * @since 1.0.0
 */

function sfw_get_spreadshirt_id_by_term( $term ) {

  $term = get_term( $term );

  return sfw_get_spreadshirt_id_by_wpobj( $term );
}




/**
 * Retrieve a Spreadshirt Id for a given post
 *
 * @see sfw_get_spreadshirt_id_by_wpobj
 *
 * @param  WP_Post $term
 *
 * @return false|SFW_Entity
 *
 * @since 1.0.0
 */

function sfw_get_spreadshirt_id_by_post( $post ) {

  $post = get_post( $post );

  return sfw_get_spreadshirt_id_by_wpobj( $post );
}




/**
 * Extract Spreadshirt Id from Entity Item
 *
 * @param  string|SFW_Entity $entity An Entity name or Entity Instance
 * @param  object $item An Entity Item
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_spreadshirt_id_by_item( $entity, $item ) {

  $entity = sfw_get_entity( $entity );

  if( !sfw_is_entity( $entity ) )
    return false;

  return $entity->get_spreadshirt_id( $item );
}





/**
 * Efficiently retrieve multiple Entity items
 *
 * @param  string|SFW_Entity $entity An Entity name or Entity Instance
 * @param  int|array $spreadshirt_ids Spreadshirt Id or Array of Ids
 *
 * @return array Array of objects
 *
 * @since  1.0.0
 */

function sfw_get_entity_items( $entity, $spreadshirt_ids ){

  $items = array();

  $entity = sfw_get_entity( $entity );

  if( !sfw_is_entity( $entity ) )
    return $items;

  $spreadshirt_ids = sfw_make_array( $spreadshirt_ids );

  // prime the requested ids
  foreach( $spreadshirt_ids as $spreadshirt_id )
    sfw_prime_entity( $entity, $spreadshirt_ids );


  // request all primed items
  sfw_load_primed( $entity );


  // look for cached items
  foreach( $spreadshirt_ids as $key => $spreadshirt_id ) {

    $maybe_id = sfw_id_cache()->get_wp_id( $entity->wp_subtype, $spreadshirt_id, $entity->wp_metakey );

    if( is_null( $maybe_id ) )
      continue;

    if( $entity->is_post() )
      $items[] = get_post( $maybe_id);
    elseif( $entity->is_term())
      $items[] = get_term( $maybe_id, $entity->wp_subtype );
  }

  return $items;
}


