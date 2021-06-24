<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Register Rest Route for Item Synchronization
 *
 * @since 1.0.0
 */

sfw_register_rest_route( 'sync-item', array(

  'callback' => function ( $request ){

    // Sync Item
    $result = sfw_sync_item(
      $request->get_param('entity'),
      $request->get_param('spreadshirt_id'),
      $request->get_param('force_update')
    );

    // create generic success message or pass WP_Error
    return is_wp_error( $result )
      ? $result
      : sfw_rest_success();
  },

  'args' => array(

    // the Entity name, like article, design, producttype
    'entity' => array(

      'required' => true,

      'validate_callback' => function( $value ) {

        return sfw_is_entity( $value );
      }

    ),

    // id of the item
    'spreadshirt_id' => array(

      'required' => true,

      'sanitize_callback' => 'sanitize_spreadshirt_id'

    ),

    // if to force the update process no matter the time of the last synchronization
    'force_update' => array(

      'required' => false,

      'type' => 'boolean',

      'default' => false

    ),

  ),

));




/**
 * Register Rest Route that ends any Sync procedure
 *
 * @since 1.0.0
 */

sfw_register_rest_route( 'did-sync', array(

  'methods' => 'GET',

  'callback' => function(){

    // save
    $fin_time = sfw_finish_sync();

    return sfw_rest_success( sprintf( __('Finished Sync at %s', 'apparelcuts-spreadshirt' ), $fin_time ) );
  },

));






/**
 * Register Rest Route for Item Synchronization
 *
 * @since 1.0.0
 */

sfw_register_rest_route( 'entity-list', array(

  'callback' => function ( $request ){


    $entity = sfw_get_entity( $request->get_param('entity') );


    $response = array(
      'offset' => $request->get_param('offset'),
      'limit' => $request->get_param('limit'),
      'entity' => $entity->name(),
      'results' => 0,
      'items' => array()
    );



    if( $entity->is_post() ) {

      $query = new WP_Query(array(

        'post_status' => implode( ',', get_post_stati() ),

        'post_type' => sfw_get_entity_posttype( $entity ),

        'posts_per_page' => $request->get_param( 'limit' ),

        'offset' => $request->get_param( 'offset' ),

      ));

      $response['count'] = intval( $query->found_posts );

      if ( $query->have_posts() ) {

        $response['results'] = intval( $query->post_count );

      	// The Loop
      	while ( $query->have_posts() ) {

          $query->the_post();
          global $post;

          $spreadshirt_id = $entity->get_spreadshirt_id( $post );

          $item = array(
            'id' => get_the_ID(),
            'type' => 'post',
            'post_status' => $post->post_status,
            'spreadshirt-id' => $spreadshirt_id,
            'last_sync' => sfw_item_last_sync( $entity, $spreadshirt_id ),
            'expired' => sfw_item_sync_expired( $entity, $spreadshirt_id )
          );

          if( $entity->is('article') )
            $item['producttype-id'] = sfw_get_producttype_id();

          $response['items'][] = $item;
      	}

      }


    }


    elseif( $entity->is_term() ) {

      $query = new WP_Term_Query(array(

        'taxonomy' => sfw_get_entity_taxonomy( $entity ),

        'hide_empty' => false,

        'number' => $request->get_param( 'limit' ),

        'offset' => $request->get_param( 'offset' ),

        'update_term_meta_cache' => true

      ));


      if ( !empty( $query->terms ) ) {

        $response['results'] = count( $query->terms );

      	// The Loop
      	foreach ( $query->terms as $term ) {

          $spreadshirt_id = $entity->get_spreadshirt_id( $term );

          $item = array(
            'id' => $term->term_id,
            'type' => 'term',
            'spreadshirt-id' => $spreadshirt_id,
            'last_sync' => sfw_item_last_sync( $entity, $spreadshirt_id ),
            'expired' => sfw_item_sync_expired( $entity, $spreadshirt_id )
          );

          $response['items'][] = $item;
      	}

      }


    }

    return $response;
  },

  'args' => array(

    // the Entity name, like article, design, producttype
    'entity' => array(

      'required' => true,

      'validate_callback' => function( $value ) {

        return sfw_is_entity( $value );
      }

    ),

    'offset' => array(

      'required' => false,

      'type' => 'integer',

      'min' => 0,

      'default' => 0
    ),

    'limit' => array(

      'required' => false,

      'type' => 'integer',

      'min' => 1,

      'max' => 1000,

      'default' => 500
    )

  ),

));








/**
 * Register Rest Route for Item Synchronization
 *
 * @since 1.0.0
 */

sfw_register_rest_route( 'trash-posts', array(

  'callback' => function ( $request ){

    foreach( $request->get_param( 'posts' ) as $post_id ) {
      do_action( 'sfw/sync/before_trash_post', $post_id );
      wp_trash_post( $post_id );
    }
    return ;
  },

  'args' => array(

    // the Entity name, like article, design, producttype
    'posts' => array(

      'required' => true,

      'validate_callback' => function( $value ) {

        return is_array( $value );
      }

    ),

  ),

));