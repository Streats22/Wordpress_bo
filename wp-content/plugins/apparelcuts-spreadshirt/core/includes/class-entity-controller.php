<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * The Entity Controller Class
 *
 */



/**
 * Manages registration of SFW Entities
 *
 * @see SFW_Entity
 * @since 1.0.0
 */

final class SFW_Entity_Controller {


  /**
   * array of all registered Entities
   *
   * @var array
   */

  public static $entities = array();




  /**
   * register an Entity
   *
   * @param  SFW_Entity $entity
   *
   * @return bool true if the Entity was registered succesfully, else false
   *
   * @since 1.0.0
   */

  public static function register( $entity ) {

    $entity = self::parse( $entity );

    if( $entity ) {

      self::$entities[ $entity->name() ] = $entity;
      return true;
    }

    return false;
  }




  /**
   * parse an Entity
   *
   * @param  SFW_Entity $entity
   *
   * @return bool
   *
   * @since 1.0.0
   */

  private static function parse( $entity ) {

    if( !$entity instanceof SFW_Entity ) {
      sfw_doing_it_wrong( __METHOD__, "No valid SFW_Entity" );
      return false;
    }

    if( isset( self::$entities[ $entity->name() ] ) ) {
      sfw_doing_it_wrong( __METHOD__, "You've tried to register an SFW_Entity that already exists" );
      return false;
    }

    return $entity;
  }




  /**
   * get the Entity name
   *
   * @param  string|SFW_Entity $mixed An Entity name or Entity Instance
   *
   * @return string an Entity name
   *
   * @since 1.0.0
   */

  private static function get_entity_name( $mixed ) {

    if( $mixed instanceof SFW_Entity )
      return $mixed->name();

    elseif( is_scalar( $mixed ) )
      return (string) $mixed;

    return '';
  }




  /**
   * retrieve an Entity
   *
   * @param  string|SFW_Entity $mixed An Entity name or Entity Instance
   *
   * @return WP_Error|SFW_Entity
   *
   * @since 1.0.0
   */

  public static function get( $mixed ){

    $name = self::get_entity_name( $mixed );

    return isset( self::$entities[ $name ] )
      ? self::$entities[ $name ]
      : new WP_Error( 'sfw-unkown-entity', __('This Entity does not exist.') );
  }




  /**
   * Check if an Entity exists
   *
   * @param  string|SFW_Entity $mixed An Entity name or Entity Instance
   *
   * @return bool
   *
   * @since 1.0.0
   */

  public static function exists( $mixed ) : bool {
    return ! is_wp_error( self::get( $mixed ) );
  }




  /**
   * Rerieve multiple Entities
   *
   * @param  array $args Arguments to filter Entities
   *
   * @return array An array of Entities. Empty array if no matches were found
   *
   * @since 1.0.0
   */

  public static function get_entities( $args ) {

    $entities = self::$entities;

    foreach( $entities as $key => $entity ) {

      foreach( $args as $property => $value ) {

        if( !property_exists( $entity, $property ) || $entity->$property != $value )
          unset( $entities[ $key ] );
      }
    }

    return $entities;
  }


} // end class SFW_Entity_Controller


