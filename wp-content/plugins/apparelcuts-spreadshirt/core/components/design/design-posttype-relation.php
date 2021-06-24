<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Retrieve Design Children Posts
 *
 * @param  string $design_id
 * @return false|array array of WP_Post
 * @since  1.0.0
 */

function sfw_get_design_children( $design_id, $from_cache = true ) {


  if( empty( $design_id ) )
    return false;


  // parent cant have children
  if( sfw_get_parent_design_id( $design_id ) ) {
    return false;
  }

  $found = false;

  if( $from_cache )
    $posts = wp_cache_get( $design_id, 'design_children_query', false, $found );

  if( !$found ) {

    $args = array(
      'post_type' => 'sfw-design',
      'post_status' => 'any',
      'meta_key' => 'sfw-parent-design',
      'meta_value' => $design_id,
      'posts_per_page' => -1
    );

    $posts = get_posts( $args );
    wp_cache_set( $design_id, $posts, 'design_children_query' );
  }


  if( empty( $posts ) || !is_array( $posts ) ) {
    return false;
  }

  sfw_maybe_cache_object_spreadshirt_ids( $posts );

  return $posts;
}




 /**
 * Save the count of articles associated with this design with
 *
 * @ignore
 * @param $design_id - see 'sfw_get_design_id' for details
 * @since 1.0.0
 */

 function _sfw_update_design_article_count( $design_id = false ) {

   $design_id = sfw_get_design_id( $design_id );

    if( empty( $design_post = sfw_get_design_post( $design_id ) ) ) {
      return 0;
    }

    // collect design ids to search for
    $search = array( $design_id );


    $children = sfw_get_design_children( $design_id );

    if( $children ) {
      foreach( $children as $_post )
        $search[] = get_post_meta( $_post->ID, sfw_get_entity_wp_metakey('design'), true );
    }

    // query for all posts except trashed with this design
    $query = new WP_Query();

    $query->query( array(
      'post_status' => 'any',
      'post_type' => 'sfw-product',
      'meta_key' => '_design-id',
      'meta_compare' => 'IN',
      'meta_value' => $search,
      'posts_per_page' => -1,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false
    ));

    $count = 0;
    $count_published = 0;
    $count_children = $children ? count( $children ) : 0;

    if( is_array( $query->posts ) ) {

      $count = count( $query->posts );

      foreach( $query->posts as $_p )
        if( $_p->post_status == 'publish' )
          $count_published++;
    }

    update_post_meta( $design_post->ID, '_article-count-total', $count );
    update_post_meta( $design_post->ID, '_article-count', $count_published );
    update_post_meta( $design_post->ID, '_children-count', $count_children );

    // maybe update parent too
    $maybe_parent = sfw_get_parent_design_id( $design_id );

    if( !empty( $maybe_parent ) )
      _sfw_update_design_article_count( $maybe_parent );
 }




/*
 * Updates design article count
 */

 add_action( 'sfw/update_design_article_count', function( $design_ids ) {

   $design_ids = sfw_make_array( $design_ids );

   foreach( $design_ids as $design_id )
     _sfw_update_design_article_count( $design_id );
 });




/**
* Add preview image and wrap title in related-designs select
*
* @ignore
* @since 1.0.0
*/

function _sfw_hook_add_thumbnails_to_design_post_select( $title, $post, $field, $post_id ) {

  $title = '<img class="preview" src="'.sfw_create_preview_url( $post, array( 250, 250 ) ).'"/>'.
           '<span class="list-post-title" title="">'.$title.'</span>';


  return $title;
}

add_filter( 'acf/fields/post_object/result/name=sfw-parent-design-post-id', '_sfw_hook_add_thumbnails_to_design_post_select', 100, 4 );






/**
* Manipulate related-designs search query to prevent selecting designs that are a child design
*
* Tests against value of 'sfw-main-design'. For parent designs its set to its Design Id, for
* child Designs its set to their parent Designs Id.
*
* @ignore
* @since 1.0.0
*/

function _sfw_hook_manipulate_search_related_designs_query( $args, $field, $post_id ) {

  $args['post__not_in'] = array( $post_id );

  $args['meta_query'] = array(

   'relation' => 'OR',

    array(
     'key' => 'sfw-parent-design',
     'compare' => 'NOT EXISTS',
     'value' => '' // This is ignored, but is necessary...
    ),

    // the following is important to get fields that may where Parent/Child Designs in the past
    array(
     'key' => 'sfw-parent-design',
     'value' => ''
    )

  );

  return $args;
}

add_filter('acf/fields/post_object/query/name=sfw-parent-design-post-id', '_sfw_hook_manipulate_search_related_designs_query', 10, 3 );




/**
 * Updates design article count on design post save
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_update_article_count_on_design_save( $post_id, $post ) {

  if( $post->post_type == 'sfw-design' ) {

    $design_id = get_post_meta( $post_id, sfw_get_entity_wp_metakey('design'), true );

    _sfw_update_design_article_count( $design_id );
  }
}

add_action( 'wp_insert_post', '_sfw_hook_update_article_count_on_design_save', 10, 2 );




/**
* Check if Designs were removed from a Parent Design
*
* fires before acf saves fields
*
* @ignore
* @since 1.0.0
*/

function _sfw_hook_check_related_designs(  $post_id  ) {

  // bail early if no ACF data
  if( empty($_POST['acf']) )
    return;

  if( get_post_type( $post_id ) !== 'sfw-design' )
    return;

  if( !isset( $_POST['acf'][ sfw_field_key('sfw-parent-design-post-id') ] ) )
    return;

  // get parent id
  $_parent_id = $_POST['acf'][ sfw_field_key('sfw-parent-design-post-id') ];
  $_previous_parent_id = get_post_meta( $post_id, 'sfw-parent-design-post-id', true );

  // bail, no change
  if( $_parent_id === $_previous_parent_id )
    return;


  if( !empty( $_parent_id ) ) {
    $_parent_design_id = get_post_meta( $_parent_id, sfw_get_entity_wp_metakey('design'), true );
    $_POST['acf'][sfw_field_key('sfw-parent-design')] = $_parent_design_id;

    // refresh article count after acf has saved the new data
    add_action( 'wp_insert_post', function() use ( $_parent_design_id ){

      _sfw_update_design_article_count( $_parent_design_id );

    });
  }
  else {

    $_POST['acf'][sfw_field_key('sfw-parent-design')] = '';
  }

  // make sure to update previous parents article count if not empty
  if( !empty( $_previous_parent_id ) ) {

    // refresh article count after acf has saved the new data
    add_action( 'wp_insert_post', function() use ( $_previous_parent_id ){

      _sfw_update_design_article_count( $_previous_parent_id );

    });

  }

}

add_action('acf/save_post', '_sfw_hook_check_related_designs', 1 );




/**
 * Updates design count everytime a designs post status changes
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_update_article_count_on_design_post_status_change( $new_status, $old_status, $post ) {

  // bail
  if( $post->post_type != 'sfw-design' )
    return;

  // bail if the status did not change the public visibility
  if( 'publish' != $new_status && 'publish' != $old_status )
    return;

  _sfw_update_design_article_count( get_post_meta( $post->ID, sfw_get_entity_wp_metakey('design'), true ) );
}

add_action(  'transition_post_status',  '_sfw_hook_update_article_count_on_design_post_status_change', 10, 3 );




/**
 * Updates design count everytime an articles post status changes
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_update_article_count_on_article_post_status_change( $new_status, $old_status, $post ) {

  // bail
  if( $post->post_type != 'sfw-product' )
    return;

  // bail if the status did not change the public visibility
  if( 'publish' != $new_status && 'publish' != $old_status )
    return;

  $design_ids = get_post_meta( $post->ID, '_design-id', false );

  if( is_array( $design_ids ) && !empty( $design_ids ) )
    foreach( $design_ids as $design_id )
      _sfw_update_design_article_count( $design_id );
}

add_action(  'transition_post_status',  '_sfw_hook_update_article_count_on_article_post_status_change', 10, 3 );




/**
 * Removes parent design from all children of a design when its post
 * status changes to some non public state
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_remove_design_children_when_unpublish_parent( $new_status, $old_status, $post ) {

  // bail
  if( $post->post_type != 'sfw-design' )
    return;

  // bail if the status was not publish anyway
  if( 'publish' != $old_status )
    return;

  $design_id = get_post_meta( $post->ID, sfw_get_entity_wp_metakey('design'), true );

  sfw_maybe_unrelate_design_children( $design_id );
}

add_action(  'transition_post_status',  '_sfw_hook_remove_design_children_when_unpublish_parent', 10, 3 );




/**
* Update article count when Designs whenever a post is trashed or deleted
*
* @ignore
* @since 1.0.0
*/

function _sfw_hook_unrelate_child_designs_when_deleting_parent( $post_id ) {

  $post = get_post( $post_id );

  if( empty( $post ) || get_post_type( $post ) !== 'sfw-design' )
    return;

  $design_id = get_post_meta( $post->ID, sfw_get_entity_wp_metakey('design'), true );

  sfw_maybe_unrelate_design_children( $design_id );
}

add_action( 'before_delete_post', '_sfw_hook_unrelate_child_designs_when_deleting_parent' );




/**
 * Removes a design as parent from its children designs
 *
 * @param  string $design_id
 * @since  1.0.0
 */

function sfw_maybe_unrelate_design_children( $design_id ) {

  $maybe_children = sfw_get_design_children( $design_id );

  // loop through children if they exist
  if( $maybe_children )
    foreach( $maybe_children as $_post ) {

      update_field( 'sfw-parent-design', '', $_post );
      update_field( 'sfw-parent-design-post-id', '', $_post );
      _sfw_update_design_article_count( $_post );
    }
}


