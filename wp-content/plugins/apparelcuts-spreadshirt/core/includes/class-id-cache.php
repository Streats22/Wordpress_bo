<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Manages the caching of relations between Spreadshirt Ids and Wordpress Ids
 */



/**
 * Manages the caching of relations between Spreadshirt Ids and Wordpress Ids
 *
 * @since 1.0.0
 */

class SFW_Id_Cache {




  /**
   * Stores Id relations
   *
   * How the data is stored:
   *    array(
   *      'sfw-product+spreadshirt-id' => array(
   *        295 ( post_id ) => 244 ( spreadshirt_id )
   *      )
   *    )
   *
   * @var array
   */

  protected $cache = array();



  /**
   * Initiation
   *
   * @since 1.0.0
   */

  function __construct() {

    // flush cache
    add_action( 'wp_insert_post', array( $this, 'flush' ) );
    add_action( 'created_term',   array( $this, 'flush' ) );

  }




  /**
   * Flushes the Id cache
   *
   * @return  void
   *
   * @since 1.0.0
   */

  public function flush() {

    $this->cache = array();
  }




  /**
   * Retrieve cached id relations
   *
   * @param   string $wp_type  A key identifying the wordpress type. can be post, term, custom post type or taxonomy
   * @param   string $meta_key the metakey storing the id
   *
   * @return  array  the known relations
   *
   * @since 1.0.0
   */

  protected function &get_group( $wp_type, $meta_key ) {

    $groupkey = $wp_type . '+' . $meta_key;

    if( !isset( $this->cache[ $groupkey ] ) )
      $this->cache[ $groupkey ] = array();

    return $this->cache[ $groupkey ];
  }




  /**
   * Set an id relation
   *
   * It is important that the same value of wp_type is used to set and get an id relation
   *
   * @param   string $wp_type  A key identifying the wordpress type. can be post, term, custom post type or taxonomy
   * @param   int $wp_id A Wordpress post or term id
   * @param   string $spreadshirt_id A Spreadshirt Id
   * @param   string $meta_key the metakey storing the id
   *
   * @since 1.0.0
   */

  function set( $wp_type, $wp_id, $spreadshirt_id, $meta_key ){

    $group = &$this->get_group( $wp_type, $meta_key );

    $group[ $wp_id ] = $spreadshirt_id;
  }




  /**
  * Retrieve a Spreadshirt Id
  *
  * @param   string $wp_type  A key identifying the wordpress type. can be post, term, custom post type or taxonomy
  * @param   int $wp_id A Wordpress post or term id
  * @param   string $meta_key the metakey storing the id
  *
  * @since 1.0.0
  */

  public function get_spreadshirt_id( $wp_type, $wp_id, $meta_key ) {

    $group = $this->get_group( $wp_type, $meta_key );

    return isset( $group[ $wp_id ] )
      ? $group[ $wp_id ]
      : null;
  }




  /**
  * Retrieve a Wordpress Id from Spreadshirt Id
  *
  * @param   string $wp_type  A key identifying the wordpress type. can be post, term, custom post type or taxonomy
  * @param   string $spreadshirt_id A Spreadshirt Id
  * @param   string $meta_key the metakey storing the id
  *
  * @return int|void A Wordpress id or null if nothing was found
  *
  * @since 1.0.0
  */

  public function get_wp_id( $wp_type, $spreadshirt_id, $meta_key ) {

    $group = $this->get_group( $wp_type, $meta_key );

    $key = array_search( $spreadshirt_id, $group );

    return false === $key
      ? null
      : $key;
  }



} // - end class SFW_Id_Cache





/**
 * Retrieve the global ID Cache Instance
 *
 * @see SFW_Id_Cache
 *
 * @return object Instance of SFW_Id_Cache
 *
 * @since 1.0.0
 */

function sfw_id_cache() {

  global $sfw_id_cache;

  if( !$sfw_id_cache instanceof SFW_Id_Cache ) {
    $sfw_id_cache = new SFW_Id_Cache();
  }

  return $sfw_id_cache;
}




/**
 * Set an ID relation
 *
 * @param   string $wp_type  A key identifying the wordpress type. can be post, term, custom post type or taxonomy
 * @param   int $wp_id A Wordpress post or term id
 * @param   string $spreadshirt_id A Spreadshirt Id
 * @param   string $meta_key the metakey storing the id
 *
 * @since 1.0.0
 */

function sfw_cache_id_relation( $wp_type, $wp_id, $spreadshirt_id, $meta_key ) {

  sfw_id_cache()->set( $wp_type, $wp_id, $spreadshirt_id, $meta_key );
}
