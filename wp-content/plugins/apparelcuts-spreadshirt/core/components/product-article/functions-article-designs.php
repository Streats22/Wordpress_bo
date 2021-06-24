<?php if ( ! defined( '\ABSPATH' ) ) exit;




  /**
  * Retrieve a design loop
  *
  * @param  mixed $article_selector See sfw_get_article_id()
  * @since 1.0.0
  */

  function sfw_get_design_loop( $article_selector = false ) {


    if( false === ( $article_id = sfw_get_article_id( $article_selector ) ) )
      return false;


    $found = false;
    $design_loop = wp_cache_get( $article_id, 'design-loop', false, $found );

    if( !$found ) {

      $article_post = sfw_get_article_post( $article_id );
      $design_terms = get_post_meta( $article_post->ID, '_design-id' );

      $design_loop = !empty( $design_terms )
        ? new Sfw_Node_Loop( $design_terms )
        : false;

      wp_cache_set( $article_id, $design_loop, 'design-loop' );
    }

    return $design_loop;
  }




  /**
  * Iterate through Views. Use it like have_posts
  *
  * @param  mixed $article_selector See sfw_get_article_id()
  * @return true|false
  * @since 1.0.0
  */

  function sfw_have_designs( $article_selector = false ) {

    $article_id = sfw_get_article_id( $article_selector );

    if( empty( $design_loop = sfw_get_design_loop( $article_id ) ) )
      return false;


    $have = $design_loop->have_nodes();

    // the loop changed, so refresh cache
    wp_cache_set( $article_id, $design_loop,  'design-loop' );


    return $have;
  }




  /**
  * Check if the View Loop is runnig
  *
  * @param  mixed $article_selector See sfw_get_article_id()
  * @return bool
  * @since 1.0.0
  */

  function sfw_designs_in_the_loop( $article_selector = false ) : bool {

    return !empty( $design_loop = sfw_get_design_loop( $article_selector ) )
      ? $design_loop->in_the_loop()
      : false;
  }




  /**
  * Retrieve the design Id
  *
  * @param  mixed $article_selector See sfw_get_article_id()
  * @return false|object
  * @since 1.0.0
  */

  function sfw_get_current_design_id( $article_selector = false ) {

    $design_loop = sfw_get_design_loop( $article_selector );

    return $design_loop->in_the_loop()
      ? $design_loop->current_node()
      : false;
  }




  /**
   * Echoes the design Id
   *
   * @param  mixed $article_selector See sfw_get_article_id()
   * @return [type]
   * @since  1.0.0
   */

  function sfw_current_design_id( $article_selector = false ) {

    echo sfw_get_current_design_id( $article_selector );
  }



