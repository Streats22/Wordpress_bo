<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Retrieve the last time a Sync was finished
 *
 * @return int Timestamp of last Synchronization
 *
 * @since 1.0.0
 */

function sfw_get_last_sync() {

  return get_option( 'sfw-last-sync', 0 );
}




/**
 * Set the time for the last finished Synchronization to now
 *
 * @return int Timestamp now
 */

function sfw_finish_sync( ) {

  $time = time();

  update_option( 'sfw-last-sync', $time, 'yes' );

  do_action( 'sfw/sync/finished' );

  return $time;
}




/**
 * Check if a Synchronization was peformed within the given duration
 *
 * @param  int $duration Time in milliseconds
 *
 * @return bool Returns true if a Synchronization was performed within the given duration
 *
 * @since 1.0.0
 */

function sfw_synced_since( $duration = WEEK_IN_SECONDS ) : bool {

  return sfw_is_synced() && sfw_get_last_sync() > time() - $duration;
}




/**
 * Check if a Synchronization has ever happened
 *
 * @return boolean
 *
 * @since 1.0.0
 */

function sfw_is_synced() : bool {

  return sfw_get_last_sync() > 0;
}




/**
 * Sync multiple Entity Items
 *
 * Synchronizes multiple Items as long as no error happens
 *
 * @param  mixed $entity
 * @param  string $spreadshirt_id A Spreadshirt Id
 * @param  boolean $force_update If Updates should be forced
 *
 * @return true|WP_Error Returns true if all items were successfully synced, else WP_Error
 *
 * @since 1.0.0
 */

function sfw_sync_items( $entity, $spreadshirt_ids, $force_update = false ) {

  if( is_array( $spreadshirt_ids ) && !empty( $spreadshirt_ids ) ) {

    foreach( $spreadshirt_ids as $spreadshirt_id ) {

      $result = sfw_sync_item( $entity, $spreadshirt_id, $force_update );

      if( is_wp_error( $result ) )
        return $result;

    }

  }

  return true;
}




/**
 * Synchronize an Item
 *
 * Triggers Creation or Update of an Entity item
 *
 * @param  mixed  $entity An Entity
 * @param  string $spreadshirt_id A Spreadshirt Id
 * @param  boolean $force_update If Updates should be forced
 *
 * @return string|WP_Error WP_Error or string indicating the performed action 'create', 'update' or 'skip'.
 *                         If $force_update is set to true, no items will be skipped.
 *
 * @since 1.0.0
 */

function sfw_sync_item( $entity, $spreadshirt_id, $force_update = false ) {


  // get entity
  $entity = sfw_get_entity( $entity );

  if( is_wp_error( $entity ) )
    return $entity;


  /**
   * Triggerd before an Item is synced
   *
   * @param object $entity SFW_Entity
   * @param string $spreadshirt_id A Spreadshirt Id
   *
   * @since 1.0.0
   */

  do_action( "sfw/sync/before",                 $entity, $spreadshirt_id );


  /**
   * Triggerd before an Item is synced
   *
   * @param object $entity SFW_Entity
   * @param string $spreadshirt_id A Spreadshirt Id
   *
   * @since 1.0.0
   */

  do_action( "sfw/sync/{$entity->name}/before", $entity, $spreadshirt_id );


  // try to retrieve the item
  $item   = $entity->get_wp_item( $spreadshirt_id );

  // set default action
  $action = 'skip';


  // the item does not exists, trigger creation
  if( !sfw_item_exists( $entity, $spreadshirt_id ) ) {

    // replace action
    $action = 'create';

    /**
     * Triggered before the creation or update of an Item.
     *
     * If an WP_Error is returned, the synchronization will be stopped
     *
     * @param object $entity SFW_Entity
     * @param string $spreadshirt_id A Spreadshirt Id
     * @param string $action Either create or update
     *
     * @since 1.0.0
     */

    $preparation = apply_filters( "sfw/{$action}/{$entity->name}/prepare", true, $entity, $spreadshirt_id, $action );


    if( is_wp_error( $preparation ) )
      return $preparation;

    // create
    $item   = sfw_create_item( $entity, $spreadshirt_id );

    if( is_wp_error( $item ) )
      return $item;

    // set last sync timestamp
    sfw_item_synced_now( $entity, $spreadshirt_id );

  }
  // trigger update
  elseif( $force_update || sfw_item_sync_expired( $entity, $spreadshirt_id ) ) {

    // replace action
    $action = 'update';

    /**
     * @see above
     * @ignore
     */

    $preparation = apply_filters( "sfw/{$action}/{$entity->name}/prepare", true, $entity, $spreadshirt_id, $action );

    if( is_wp_error( $preparation ) )
      return $preparation;

    //update
    $item   = sfw_update_item( $entity, $spreadshirt_id );

    if( is_wp_error( $item ) )
      return $item;

    // set last sync timestamp
    sfw_item_synced_now( $entity, $spreadshirt_id );

  }


  /**
   * Triggered after an Item may has been updated or created
   *
   * @param mixed $item Maybe an valid item, can also be WP_Error
   * @param string $spreadshirt_id A Spreadshirt Id
   * @param object $entity SFW_Entity
   *
   * @since 1.0.0
   */

  $item = apply_filters( "sfw/sync", $item, $spreadshirt_id, $entity );

  /**
   * Triggered after an Item of Entity may has been updated or created
   *
   * @param mixed $item Maybe an valid item, can also be WP_Error
   * @param string $spreadshirt_id A Spreadshirt Id
   * @param object $entity SFW_Entity
   *
   * @since 1.0.0
   */

  $item = apply_filters( "sfw/sync/{$entity->name}",  $item, $spreadshirt_id, $entity );


  if( $entity->is_valid_item( $item ) ) {

    /**
     * Triggered after an Item has been synced succesfully
     *
     * @param mixed $item An Item of Entity
     * @param string $spreadshirt_id A Spreadshirt Id
     * @param object $entity SFW_Entity
     * @param string $action The performed action 'update', 'create' or 'skip'
     *
     * @since 1.0.0
     */

    do_action( "sfw/synced",                    $item, $spreadshirt_id, $entity, $action );

    /**
     * Triggered after an Item of Entity has been synced succesfully
     *
     * @param mixed $item An Item of Entity
     * @param string $spreadshirt_id A Spreadshirt Id
     * @param object $entity SFW_Entity
     * @param string $action The performed action 'update', 'create' or 'skip'
     *
     * @since 1.0.0
     */

    do_action( "sfw/synced/{$entity->name}",    $item, $spreadshirt_id, $entity, $action );

  }

  else {

    /**
     * Triggered after the Synchronization of an Item failed
     *
     * @param object $entity SFW_Entity
     * @param string $spreadshirt_id A Spreadshirt Id
     *
     * @since 1.0.0
     */

    do_action( "sfw/sync/failed", $entity, $spreadshirt_id );

  }

  return $action;

}




/**
 * Retrieve the timestamp when an Entity Item was synced the last time
 *
 * @param object $entity SFW_Entity
 * @param string $spreadshirt_id A Spreadshirt Id
 *
 * @return false|int Timestamp of the last synchronization. Zero if no synchronization was succesfully performed yet. Returns 1 if no synchronization was performed for the current plugin version
 *
 * @since 1.0.0
 */

function sfw_item_last_sync( $entity, $spreadshirt_id ) {

  $time   = 0;

  if( sfw_item_exists( $entity, $spreadshirt_id ) ) {
    $item = sfw_get_item( $entity, $spreadshirt_id );
    $_time = sfw_get_object_metadata( $item, '_synced' );
    $_version = sfw_get_object_metadata( $item, '_synced_version' );

    if( is_numeric( $_time ) )
      $time = $_time;

    /**
     * The minimal version required for items
     */
    $min_required_item_version = apply_filters('sfw/min_required_item_version', '1.0.0' );

    // if versions don't match, require new sync
    if( version_compare( $_version, $min_required_item_version, '<' ) ) {

      $time = 1;
    }

  }

  return apply_filters( "sfw/sync/item/synced", $time, $entity, $spreadshirt_id );
}




/**
 * Mark an item as synced
 *
 * @param object $entity SFW_Entity
 * @param string $spreadshirt_id A Spreadshirt Id
 *
 * @since 1.0.0
 */

function sfw_item_synced_now( $entity, $spreadshirt_id ){

  if( sfw_item_exists( $entity, $spreadshirt_id ) ) {

    $item = sfw_get_item( $entity, $spreadshirt_id );

    sfw_update_object_metadata( $item, '_synced', time() );

    sfw_update_object_metadata( $item, '_synced_version', sfw_version() );
  }

}




/**
 * Checks if an Entity Items sync is expired and the entity should be synced again
 *
 * @param object $entity SFW_Entity
 * @param string $spreadshirt_id A Spreadshirt Id
 *
 * @return bool True if a Synchronization of an Item is expired
 *
 * @since 1.0.0
 */

function sfw_item_sync_expired( $entity, $spreadshirt_id ) : bool {

  $entity  = sfw_get_entity( $entity );

  if( !sfw_is_entity( $entity ) )
    return false;

  $expire  = property_exists( $entity, 'sync_expire' ) ? $entity->sync_expire : MONTH_IN_SECONDS;

  $expire = apply_filters( 'sfw/sync/entity_expire', $expire, $entity, $spreadshirt_id );

  $expired = time() - $expire;

  $synced  = sfw_item_last_sync( $entity, $spreadshirt_id );

  return $synced < $expired;
}



