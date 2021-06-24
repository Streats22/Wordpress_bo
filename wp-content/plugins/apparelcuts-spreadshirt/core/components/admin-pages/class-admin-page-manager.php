<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
 * Handles registration of SFW_Admin Pages
 *
 * @since 1.0.0
 */

class SFW_Admin_Page_Manager {




	/**
	 * the registered pages
	 *
	 * @var array
	 */

	var $pages = array();




	/**
	 * Default position of new pages, relative to the main menu
	 *
	 * @var integer
	 */

	var $auto_menu_position_offset = 0.1;




	/**
	 * @ignore
	 */

  function __construct() {
    //
  }




  /**
   * validates page with defaults
   *
   * @param  array $page An array with arguments for the page
   * @return array The validated page
   * @since 1.0.0
   */

	private function validate_page( $page ) {

		// calculate default position
		$this->auto_menu_position_offset += 0.0005;
		$default_pos = (string) ( sfw_admin_get_parent_menu_position() + ( $this->auto_menu_position_offset ) );

		// for parent page
		if( sfw_admin_get_parent_menu_slug() === @$page['menu_slug'] ) {
			$page['position'] = (string) ( (float) sfw_admin_get_parent_menu_position() + 0.0002 );
			$page['parent_slug'] = '';
		}

		$page = wp_parse_args($page, array(
			'page_title' 		  => '',
			'menu_title'	    => '',
			'menu_slug' 		  => '',
			'capability'		  => sfw_get_manage_cap(),
			'parent_slug'		  => sfw_admin_get_parent_menu_slug(),
			'position'			  => $default_pos,
			'icon_url'			  => false,
			'redirect'		 	  => true,
			// number of metabox columns, max is 2
			'columns'					=> 2,
			// allow acf field groups to be loaded
      'acf'             => true,
			// autoload options from this page
			'autoload'			  => false,
			// post id for saving options
			'post_id'				  => 'options',
			// callback, automatically creates metabox
      'metabox'         => false,
      'metabox_title'   => '&nbsp;',
			'template'				=> dirname(__FILE__) . '/template-admin-pages-html.php',
			'update_button'		=> __('Update', 'apparelcuts-spreadshirt' ),
			'updated_message'	=> __('Options Updated', 'apparelcuts-spreadshirt' ),
		));


		if( empty($page['menu_title']) )
			$page['menu_title'] = $page['page_title'];



		if( empty($page['menu_slug']) )
			$page['menu_slug'] = 'sfw-options-' . sanitize_title( $page['menu_title'] );


		return $page;

	}




  /**
   * add a new admin page
   *
   * @param array $page
   * @since 1.0.0
   */

	function add_page( $page ) {

		// delay adding pages until the plugins and themes could overwrite the main page
		add_action( 'after_setup_theme', function() use ( $page ){

			return $this->register_page( $page );
		});
	}




  /**
   * add a new admin page
   *
   * @param array $page
   * @since 1.0.0
   */

	private function register_page( $page ) {

		// validate
		$page = $this->validate_page( $page );
		$slug = $page['menu_slug'];


		// append
		$this->pages[$slug] = $page;


		// return
		return $page;

	}




	/**
	 * update_page
	 *
   * @param array $page
   * @since 1.0.0
	 */

	function update_page( $slug = '', $data = array() ) {

		if( $this->maybe_doing_it_wrong() )
			return false;

		// vars
		$page = $this->get_page( $slug );


		// bail early if no page
		if( !$page )
			return false;


		// loop
		$page = array_merge( $page, $data );


		// set
		$this->pages[ $slug ] = $page;


		// return
		return $page;

	}




  /**
   * Retrieve a page
   *
   * @param  string $slug
   * @return array|null
   * @since 1.0.0
   */

	function get_page( $slug ) {

		if( $this->maybe_doing_it_wrong() )
			return;

		return isset( $this->pages[$slug] ) ? $this->pages[$slug] : null;

	}




  /**
   * Retrieve all pages
   *
   * @return array
   * @since 1.0.0
   */

	function get_pages() {

		if( $this->maybe_doing_it_wrong() )
			return [];

		return $this->pages;

	}





	/**
	 * Check if object was used too early
	 *
	 * @return bool
	 * @since  1.0.0
	 */

	function maybe_doing_it_wrong() {

		if( !did_action('after_setup_theme')){
			sfw_doing_it_wrong( __METHOD__, 'You can\'t update pages before \'after_setup_theme\'' );
			return true;
		}

		return false;
	}

}

