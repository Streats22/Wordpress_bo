<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * An SFW_Entity is a Relation between an Spreadshirt Resource like an Article and a Wordpress
 * Resource like a Custom Post Type or Taxonomy. It is identified by its name property
 *
 * We call Posts and Terms 'item' in this context.
 *
 * @since 1.0.0
 */




/**
 * Control relation between Wordpress Resources and Spreadshirt Resources
 *
 * @since 1.0.0
 */

class SFW_Entity {




  /**
   * Creates Instance
   *
   * @param  string $name A unique Entity name
   * @param  array $properties
   *
   * @todo Add Docmentation for Properties
   *
   * @return WP_Error|SFW_Entity Returns new Entity on Success
   *
   * @since 1.0.0
   */

  function __construct( $name, $properties ) {

    $this->name = $name;

    $properties = wp_parse_args( $properties, array(

      // post, term
      'wp_type'       => 'post',

      // posttype, taxonomy
      'wp_subtype'   => null,

      // metakey
      'wp_metakey'  => '_spreadshirt-id',

      // create callback
      'create_callback' => null,

      // update callback
      'update_callback' => null,

      // sync expire
      'sync_expire' => WEEK_IN_SECONDS

    ) );

    foreach( $properties as $property => $value )
      $this->$property = $value;

    SFW_Entity_Controller::register( $this );

  }




  /**
   * Retieve an Entity's name
   *
   * @return string the name
   *
   * @since 1.0.0
   */

  public function name() {

    return $this->name;

  }




  /**
   * Check if the Entity's wp_type is 'term'
   *
   * @return boolean
   *
   * @since  1.0.0
   */

  public function is_term() {
    return $this->wp_type === 'term';
  }




  /**
   * Check if the Entity's wp_type is 'post'
   *
   * @return boolean
   *
   * @since  1.0.0
   */

  public function is_post() {
    return $this->wp_type === 'post';
  }




  /**
   * Checks if this entity matches any of names
   *
   * @param  string|array  $names An Entity's name or array of names
   * @return boolean
   *
   * @since 1.0.0
   */

  public function is( $names ) {

    $names = sfw_make_array( $names );

    return in_array( $this->name(), $names );;
  }



  /**
   * Checks if the given value is a valid items for this Entity
   *
   * @param  WP_Post|WP_Term $item
   *
   * @return boolean
   *
   * @since 1.0.0
   */

  public function is_valid_item( $item ) {

    $retval = false;

    if( $this->is_post() )
      $retval = sfw_is_wp_post( $item );
    elseif( $this->is_term() )
      $retval = sfw_is_wp_term( $item );

    return apply_filters( 'sfw/entity/validate_item', $retval, $item, $this );
  }




  /**
   * Retrieve an Item of this Entity
   *
   * @param  string $spreadshirt_id A Spreadshirt Id
   *
   * @return object|false Returns post or term on success, else false
   *
   * @since 1.0.0
   */

  public function get_wp_item( $spreadshirt_id ){

    $items = sfw_get_entity_items( $this, $spreadshirt_id );
    $item  = empty( $items ) ? false : $items[0];

    return apply_filters( 'sfw/entity/get_wp_item', $item, $this, $spreadshirt_id );
  }




  /**
   * Returns Spreadshirt Id of a given Item
   *
   * @param  WP_Post|WP_Term $item
   *
   * @return false|string A Spreadshirt Id
   *
   * @since  1.0.0
   */

  public function get_spreadshirt_id( $item ) {

    if( !$this->is_valid_item( $item ) )
      return false;

    return sfw_get_object_metadata( $item, $this->wp_metakey, true );
  }




  /**
   * Creates a new Item for this Entity
   *
   * @param  string $spreadshirt_id A Spreadshirt Id
   *
   * @return WP_Error|object Returns a new Item on success, else an WP_Error indicating the reason
   *
   * @since 1.0.0
   */

  public function create( $spreadshirt_id ){

    // bail
    if( $this->get_wp_item( $spreadshirt_id ) ) {
      return sfw_create_error( 'entity-creation', 'The Item does already exist', array( $spreadshirt_id, $this->name() ) );
    }


    // filter before
    do_action( "sfw/create/{$this->name}/before", $this, $spreadshirt_id );

    // try to create the item
    $maybe_item = is_callable( $this->create_callback )
      ? call_user_func( $this->create_callback, $spreadshirt_id )
      : sfw_create_error( 'entity-creation',  'No creation_callback specified.', array( $spreadshirt_id, $this->name() ) );

    $maybe_item = apply_filters( "sfw/create", $maybe_item, $spreadshirt_id, $this );
    $maybe_item = apply_filters( "sfw/create/{$this->name}", $maybe_item, $spreadshirt_id, $this  );

    // double check that we return an error if the item is not valis
    if( !$this->is_valid_item( $maybe_item ) && !is_wp_error( $maybe_item ) )
      $maybe_item = new WP_Error( 'sfw-entity-creation',
        sprintf( 'The Item of type %1$s with Id %2$s could not be created.', $this->wp_type, $spreadshirt_id )
      );

    return $maybe_item;
  }





  /**
   * Updates an Item for this Entity
   *
   * @param  string $spreadshirt_id A Spreadshirt Id
   *
   * @return WP_Error|object Returns a new Item on success, else an WP_Error indicating the reason
   *
   * @since 1.0.0
   */

  public function update( $spreadshirt_id ){

    $item = $this->get_wp_item( $spreadshirt_id );

    // bail
    if( !$this->is_valid_item( $item ) ) {
      return new WP_Error( 'sfw-entity-update', __('The Item does not exist or is broken.') );
    }

    // try to create the item
    if( is_callable( $this->update_callback ) )
      $item = call_user_func( $this->update_callback, $item );

    $item = apply_filters( "sfw/update",               $item, $spreadshirt_id, $this );
    $item = apply_filters( "sfw/update/{$this->name}", $item, $spreadshirt_id, $this );


    return $item;
  }


} // - end class SFW_Entity

