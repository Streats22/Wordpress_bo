<?php if ( ! defined( '\ABSPATH' ) ) exit;





/**
 * Controls SFW dynamic Pages
 *
 * @since  1.0.0
 */

class SFW_Dynamic_Page_Controller {




  /**
   * array of registered dynamic pages
   *
   * @var array
   */

  var $pages = array();



  /**
   * Initiate all dynamic pages
   *
   * @since  1.0.0
   */

  function load() {

    $self = $this;

    // update pages
    add_action( 'sfw/activate_shop',         array( $this, '_hook_check_pages_and_force_creation') );
    add_action( 'sfw/prepare-shop-settings', array( $this, '_hook_check_pages_and_force_creation') );


    add_action( 'sfw/init', array( $this, '_hook_register_page_options') );

    // add post status for all pages
    add_action( 'sfw/init', array( $this, '_hook_append_special_post_status') );


    // permalink handling for required pages
    add_filter( 'sfw/js', array( $this, '_hook_add_permalinks_to_js') );
    add_action( 'sfw/shopoptions/updated', array( $this, '_hook_update_options') );
    add_action( 'save_post', array( $this, '_hook_save_post_update_options') );


  }




  /**
   * Registers a new dynamic Page
   *
   * @param  array $page
   * @return WP_Error|true
   * @since  1.0.0
   */

  function register_page( $page ) {
    /*
    if( did_action( 'acf/init' ) ) {

      sfw_doing_it_wrong( __METHOD__, __('Page registered too late', 'apparelcuts-spreadshirt' ) );

      return sfw_error( __('Page registered too late', 'apparelcuts-spreadshirt' ) );
    }
    */

    $page = $this->parse_page( $page );

    if( is_wp_error( $page ) )
      return $page;

    $this->pages[ $page['slug'] ] = $page;

    return true;
  }




  /**
   * Remove a registered Page
   *
   * @param  string $name
   * @since  1.0.0
   */

  function unregister_page( $name ) {

    if( isset( $this->pages[ $name ] ) )
      unset( $this->pages[ $name ] );
  }




  /**
   * page prefix
   *
   * @var string
   */

  var $prefix = 'sfw_page_';




  /**
   * Parse a page
   *
   * @param  string|array $page Page options or page label
   * @return array|WP_Error
   * @since  1.0.0
   */

  private function parse_page( $page ) {

    if( is_string( $page ) )
      $page = array( 'slug' => sanitize_key( $page ), 'label' => $page );

    if( !is_array( $page ) || empty( $page['slug'] ) )
      return sfw_error();

    $page = wp_parse_args( $page, array(
      'name' => $this->prefix.$page['slug'],
      'required' => true,
      'instructions' => '',
      'label' => $page['slug'],
      '_post_type' => 'page',
      // post settings
      'post_title' => 0,
      'post_name' => false,
      'post_status' => 'publish',
      'post_content' => '',
      // automatically create a default page
      'is_required_page' => true,
      // show special post state
      'display_state' => true,
    ) );

    if( empty( $page['post_title'] ) )
      $page['post_title'] = $page['label'];

    return apply_filters( 'sfw/parse_dynamic_page', $page );
  }




  /**
   * Removes the prefix from string
   *
   * @param  string $string
   * @return string String without page prefix
   * @since  1.0.0
   */

  function strip_prefix( $string ) {

    return 0 === strpos( $string, $this->prefix )
      ? substr( $string, strlen( $this->prefix ) )
      : $string;
  }




  /**
   * Retrieve a registered page
   *
   * @param  string $slug
   * @return false|array
   * @since  1.0.0
   */

  function get_page( $slug ) {

    $slug = $this->strip_prefix( $slug );

    return isset( $this->pages[ $slug ] )
      ? $this->pages[ $slug ]
      : false;
  }




  /**
   * Retrieve all registered pages
   *
   * @return array
   * @since  1.0.0
   */

  function get_pages( ) {

    return $this->pages;
  }




  /**
   * Retrieve a page selection field for a page
   *
   * @param  string $slug
   * @return array
   * @since  1.0.0
   */

  private function get_page_acf_field( $slug ) {

    $page = $this->get_page( $slug );

    $field = wp_parse_args( $page, array(
			'key' => 'field_'.$slug,
			'label' => '',
			'name' => '',
			'type' => 'post_object',
			'required' => 1,
			'post_type' => array(
				0 => $page['_post_type'],
			),
			'taxonomy' => '',
			'allow_null' => 0,
			'multiple' => 0,
			'return_format' => 'id',
			'ui' => 1,

      // parent
      'parent' => sfw_field_key( 'group-dynamic-page-settings' ),

      // mark as sfw_page
      'sfw_page' => true
		) );

    return $field;
  }




  /**
   * Registers an ACF for every page
   *
   * @since  1.0.0
   */

  function _hook_register_page_options() {

    foreach( $this->get_pages() as $name => $page ) {
      if( !$page['is_required_page'] )
        continue;

      $field = $this->get_page_acf_field( $name );
      acf_add_local_field( $field );

      if( isset( $page['acf_append'] ) ) {
        sfw_acf_append_to_field( $field['key'], $page['acf_append'] );
      }
    }

  }




  /**
   * Makes default pages available in Javascript
   *
   * @since  1.0.0
   */

  function _hook_add_permalinks_to_js( $args ) {

    $args['pages'] = array();

    foreach( $this->get_pages() as $name => $page ) {
      if( !$page['is_required_page'] )
        continue;

      $args['pages'][$name] = get_option( 'sfw-page-'.$name ); //get_permalink( $this->get_page_id( $name ) );
    }

    return $args;
  }




  /**
   * Saves links as autoload options to spare some database queries
   *
   * @since  1.0.0
   */

  function _hook_update_options(  ) {

    foreach( $this->get_pages() as $name => $page ) {
      if( !$page['is_required_page'] )
        continue;

      update_option( 'sfw-page-'.$name, get_permalink( $this->get_page_id( $name ) ), 'yes' );
    }

  }

  /**
   * Saves links as autoload options to spare some database queries
   *
   * @since  1.0.0
   */

  function _hook_save_post_update_options( $post_id ) {

    foreach( $this->get_pages() as $name => $page ) {
      if( !$page['is_required_page'] )
        continue;

      if( $this->get_page_id( $name ) == $post_id )
        update_option( 'sfw-page-'.$name, get_permalink( $post_id ), 'yes' );
    }

  }



  /**
   * Creates and sets Pages that do not already exist
   *
   * @since  1.0.0
   */

  function _hook_check_pages_and_force_creation() {

    if( ! sfw_is_shop_properly_configured() )
      return;

    foreach( $this->get_pages() as $slug => $page ) {

      if( !$page['is_required_page'] )
        continue;


      $page_id = get_field( $page['name'], 'option' );
      $post = $page_id ? get_post( $page_id ) : false;

      if( !sfw_is_wp_post( $post ) || $post->post_status != 'publish' ) {


        $args = array(
          'post_type' => $page['_post_type'],
          'post_status' => $page['post_status'],
          'post_title' => $page['post_title'],
          'post_name' => $page['post_name'],
          'post_content' => $page['post_content'],
          'meta_input' => array(
          )
        );


        $post_id = wp_insert_post( $args );

        // update page setting
        if( !is_wp_error( $post_id ) ) {
          update_field( $page['name'], $post_id, 'option' );
        }

      }

    }

  }


  /**
   * return dynamic page settings for post
   *
   * @param  int|WP_Post $post
   * @return false|array
   * @since  1.0.0
   */

  function get_dynamic_page_by_post( $post ) {

    $post = get_post( $post );

    if( !sfw_is_wp_post( $post ) )
      return false;

    foreach( $this->get_pages() as $slug => $page ) {
      if( $post->ID == get_field( $page['name'], 'option' ) )
        return $page;
    }

    return false;
  }




  /**
   * Retrieve a wp page id for dynamic page
   *
   * @param  string $slug
   * @return false|null
   * @since  1.0.0
   */

  function get_page_id( $slug ){

    $page = $this->get_page( $slug );

    return $page
      ? get_field( $page['name'], 'option' )
      : false;
  }




  /**
   * Retrieve all page slugs
   *
   * @return array
   * @since  1.0.0
   */

  function get_valid_page_layouts( ) {

    return array_keys( $this->get_pages() );
  }




  /**
   * Checks if slug is a page
   *
   * @param  string  $layout
   * @return boolean
   * @since  1.0.0
   */

  function is_valid_page_layout( $layout ) {

    return in_array( $this->strip_prefix( $layout ), $this->get_valid_page_layouts() );
  }




  /**
   * Checks if post is main page
   *
   * @param  string|array|false $check_page_slugs
   * @param  boolean $post_id
   * @return boolean
   * @since  1.0.0
   */

  function is_page( $check_page_slugs = false,  $post_id = false ) {

    $post_id = $post_id ?: get_the_ID();

    $maybe_page = $this->get_dynamic_page_by_post( $post_id );

    if( !$maybe_page )
      return false;

    // yes, any layout
    if( !$check_page_slugs )
      return true;

    // check against set of layouts
    $check_page_slugs = sfw_make_array( $check_page_slugs );

    return in_array( $maybe_page['slug'], $check_page_slugs );
  }




  /**
  * Add Post State for Pages that are a
  *
  * @since 1.0.0
  */

  function _hook_append_special_post_status() {

    if( !is_admin() )
      return;


  	add_filter('display_post_states', function ( $post_states, $post ) {

      // check if its a main page
      $maybe_page = $this->get_dynamic_page_by_post( $post );

      if( $maybe_page && $maybe_page['display_state'] ) {

  			$post_states['sfw-main-page'] = sprintf( '<span class="sfw-poststate-primary">%s</span>', $maybe_page['label'] );

      }


  		return $post_states;


  	}, 10, 2 );

  }


} // -- end class SFW_Dynamic_Page_Controller



function sfw_dynamic_page_controller() {

  global $sfw_dynamic_page_controller;

  if( !$sfw_dynamic_page_controller instanceof SFW_Dynamic_Page_Controller ) {
    $sfw_dynamic_page_controller = new SFW_Dynamic_Page_Controller();
    $sfw_dynamic_page_controller->load();
  }

  return $sfw_dynamic_page_controller;
}


