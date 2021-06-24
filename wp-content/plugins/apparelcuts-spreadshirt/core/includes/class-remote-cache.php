<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Classes for managing the caching of remote resources
 *
 * Most Spreadshirt resources should be cached for a while to improve performance and limit
 * the chance to exceed api limits. Some resources should not be cached, like Baskets .
 *
 * @since 1.0.0
 */




/**
 * Abstract class of a resource cache handler.
 *
 * You can extend this class to create a new cache system.
 *
 * @since 1.0.0
 */


abstract class SFW_Remote_Cache {




  /**
   * stores the result
   *
   * @var mixed|false
   */

  protected $result = null;




  protected $expiration_hard_boundary = 0;

  /**
   * Allows to expire caches
   */
  function expired( $expire = 0 ) {
    return apply_filters( 'sfw/cache/expired', false, $this, $expire );
  }




  /**
   * Checks if cached data for the current instance exists and is not expired
   *
   * @return mixed|false
   *
   * @since  1.0.0
   */

  protected  function process( ) {

    $data = $this->getdata( 'data' );

    if( empty( $data ) )
      return $this->set_result(false);

    $expire = $this->getdata( 'expire' );


    if( time() > $expire || $this->expired( $expire ) )
      return $this->set_result(false);

    return $this->set_result( $data );

  }




  /**
   * Fill the Cache
   *
   * @param mixed $result Any data
   *
   * @since 1.0.0
   */

  protected function set_result( $result ){

    $this->result = $result;

    return $result;
  }




  /**
   * Check if the cache contains valid data
   *
   * @return bool
   *
   * @since  1.0.0
   */

  public function empty( ) {

    return $this->get() === false;
  }




  /**
   * Retrieve data to the current instance
   *
   * @return mixed|false the cache data or false
   *
   * @since 1.0.0
   */

  public function get() {

    if( null === $this->result ) {

      $this->process();
    }

    return $this->result;
  }




  /**
   * Save data to the current instance
   *
   * @param mixed  $data
   * @param integer $expire Time to expires in seconds
   *
   * @since 1.0.0
   */

  public function set( $data, $expire = 0 ) {

    $this->setdata( 'data', $data );

    $this->setdata( 'expire', time() + $expire );
  }




  /**
   * Set any data
   *
   * @param $key A unique key
   * @param $data Any data
   *
   * @since 1.0.0
   */

  abstract function setdata( $key, $data );




  /**
   * Get any data
   *
   * @param $key A unique key
   *
   * @return mixed data
   *
   * @since 1.0.0
   */

  abstract function getdata( $key );

}






/**
 * Handle caching of resources in the transient cache
 *
 * @see SFW_Remote_Cache
 *
 * @since 1.0.0
 */

class SFW_Remote_Cache_Transient extends SFW_Remote_Cache {




  /**
   * Retrieve a Instance for key
   *
   * @return object Instance of SFW_Remote_Cache_Transient
   *
   * @since  1.0.0
   */

  static function get_instance( $key ) {

    $obj = wp_cache_get( $key, get_class() );

    if( $obj )
      return $obj;

    $instance = new static( $key );

    wp_cache_set( $key, $instance, get_class() );

    return $instance;
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function __construct( $key ) {
    $this->key = $key;
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function get( ) {
    return get_transient( $this->key );
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function set( $data, $expire = 0 ) {
    return set_transient( $this->key, $data, $expire );
  }




  // abstract functions must be defined but are not used since we replace set and get method instead
  function setdata( $key, $data ){}
  function getdata( $key ){}

}




/**
 * Handles caching of resources that are used globally
 *
 * This Cache Handler should only be used for resources that are most likely used on every page
 * of the shop. These resources will be stored as option and autoloaded
 *
 * @see SFW_Remote_Cache
 *
 * @since 1.0.0
 */

class SFW_Remote_Cache_Option_Autoload extends SFW_Remote_Cache {




  /**
   * Retrieve a Instance for option_name
   *
   * @param $option_name A unique name for storimg the data in the options table
   *
   * @return object Instance
   *
   * @since  1.0.0
   */

  static function get_instance( $option_name ) {

    $obj = wp_cache_get( $option_name, get_class() );

    if( $obj )
      return $obj;

    $instance = new static( $option_name );

    wp_cache_set( $option_name, $instance, get_class() );

    return $instance;
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function __construct( $option_name ) {

    $this->option_name = $option_name;
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function getdata( $key ) {
    return get_option( '_'. $this->option_name . '_' . $key );
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function setdata( $key, $data ) {
    return update_option(
      '_'. $this->option_name . '_' . $key,
      $data,
      'yes'
    );
  }

}




/**
 * Handles Caching of Entity Resources
 *
 * Spreadshirt resources that are bind to Wordpress resources ( we call them Entities ), like an Spreadshirt Article,
 * that always has a relative custom post type of sfw-product, are cached inside the custom
 * post types or terms meta. We deliberately desist from using the transient API, as this will
 * most probably cause either many database queries or excessive autoloads. Wordpress requests all meta
 * for the current posts as a single queries, while transient would be loaded individually
 *
 * @see SFW_Remote_Cache
 *
 * @since 1.0.0
 */

class SFW_Remote_Cache_Entity extends SFW_Remote_Cache{





  /**
   * Retrieve a Instance for Entity and Spreadshirt Id
   *
   * @param $entity_name An Entity name or Entity
   * @param $spreadshirt_id An Spreadshirt Id
   * @param string $children Use to save data in the context of this entity
   *
   * @return object Instance of SFW_Remote_Cache_Entity or Instance of SFW_Remote_Cache_Transient
   *                if no wordpress resource for the entity exists
   *
   * @since  1.0.0
   */

  static function get_instance( $entity_name, $spreadshirt_id, $children = '' ) {

    // try to get from cache first
    $key = implode('+', func_get_args() );
    $obj = wp_cache_get( $key, get_class() );

    if( $obj )
      return $obj;

    // get the entity
    $entity = sfw_get_entity( $entity_name );

    // return default cache
    if( !sfw_is_entity( $entity ) ){
      return SFW_Remote_Cache_Transient::get_instance( $key );
    }

    // check if a post exists, else return transient cache
    $wpitem = $entity->get_wp_item( $spreadshirt_id );


    // probably there doesn't exist a post for this spreadshirt resource yet, fallback to the
    // object cache. this is most likely the case when syncing items for the first time
    if( !is_object( $wpitem ) ) {
      return SFW_Object_Cache::get_instance( $key );
    }

    $instance = new static( $entity, sfw_get_wpitem_id( $wpitem ), $children );

    wp_cache_set( $key, $instance, get_class() );

    return $instance;
  }



  private $children = '';



  /**
   * @ignore
   * @since 1.0.0
   */

  function __construct( $entity, $id, $children = '' ) {

    $this->children = empty( $children ) ?: '_' . $children;
    $this->id = $id;
    $this->entity = $entity;
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function getdata( $key ) {
    return get_metadata(
      $this->entity->wp_type,
      $this->id,
      '_'. $this->entity->wp_metakey . $this->children . '_' . $key,
      true
    );
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function setdata( $key, $data ) {
    return update_metadata(
      $this->entity->wp_type,
      $this->id,
      '_'. $this->entity->wp_metakey . $this->children . '_' . $key,
      $data
    );
  }

}






/**
 * Handles Caching of resources in the pbject cache
 *
 * Only use this cache handler for resources that should be cached for the duration
 * of the current page load.
 *
 * @see SFW_Remote_Cache
 *
 * @since 1.0.0
 */

class SFW_Object_Cache extends SFW_Remote_Cache{




  /**
   * Create Instance for unique key
   *
   * @param  string|int $key A key identifying the resource
   *
   * @return object Instance
   *
   * @since  1.0.0
   */

  static function get_instance( $key ) {

    $obj = wp_cache_get( $key, get_class() );

    if( $obj )
      return $obj;

    $instance = new static( $key );

    wp_cache_set( $key, $instance, get_class() );

    return $instance;
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function __construct( $key ) {
    $this->key = $key;
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function get( ) {
    return wp_cache_get( $this->key, get_class().'_objects' );
  }




  /**
   * @ignore
   * @since 1.0.0
   */

  function set( $data, $expire = 'shhh errors' ) {
    return wp_cache_set( $this->key, $data, get_class().'_objects' );
  }




  // abstract functions must be defined but are not used since we replace set and get method instead
  function setdata( $key, $data ){}
  function getdata( $key ){}
}

