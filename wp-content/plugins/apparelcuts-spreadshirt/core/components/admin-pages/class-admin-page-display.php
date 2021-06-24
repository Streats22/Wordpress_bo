<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
 * Handles Display of SFW Admin Pages with Options
 *
 * @ignore
 * @since 1.0.0
 */

class SFW_Admin_Page_Display {




	/** @var array Contains the current options page */
	var $page;




	/**
	 * Add Actions
	 *
	 * @ignore
	 * @since 1.0.0
	 */

	function __construct() {

		// add menu items
		add_action('admin_menu', array($this,'admin_menu'), 99, 0);

		// add menu separator before and after plugin
		add_action('admin_menu', function(){
			$this->add_admin_menu_separator( (string)( sfw_admin_get_parent_menu_position() + 0.0001  ) );
			//$this->add_admin_menu_separator( sfw_admin_get_posttype_menu_position() + 1  ); 
		}, 200 );

	}




	/**
	 * Inserts menu separator at position
	 *
	 * @author MikeSchinkel
	 * @see https://wordpress.stackexchange.com/questions/2666/add-a-separator-to-the-admin-menu
	 * @param string $position The menu position
	 * @ignore
	 * @since 1.0.0
	 */

	function add_admin_menu_separator( $position ) {
		global $menu;
		$index = 0;
		foreach($menu as $offset => $section) {
			if (substr($section[2],0,9)=='separator')
				$index++;
			if ($offset>=$position) {
				$menu[$position] = array('','read',"separator{$index}",'','wp-menu-separator');
				break;
			}
		}
		ksort( $menu );
	}




	/**
	 * [admin_menu description]
	 *
	 * @ignore
	 * @since  1.0.0
	 */

	function admin_menu() {

    if( !class_exists('sfw') )
      return;


		$pages = sfw_get_admin_pages();


		// bail
		if( empty( $pages ) ) return;


		// loop
		foreach( $pages as $page ) {

			// vars
			$slug = '';

			// parent
			if( empty($page['parent_slug']) ) {

				$slug = add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], array($this, 'html'), $page['icon_url'], $page['position'] );

			// child
			} else {

				$slug = add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], array($this, 'html') );

			}


			// actions
			add_action("load-{$slug}", array($this,'admin_load'));

		}

	}



	/**
	 * [admin_load description]
	 *
	 * @ignore
	 * @since  1.0.0
	 */

	function admin_load() {

		// globals
		global $plugin_page;

		// vars
		$this->page = sfw_get_admin_page( $plugin_page );


		do_action( 'sfw/admin-page/load-'.$plugin_page );


		// add columns support
		add_screen_option('layout_columns', array('max'	=> $this->page['columns'], 'default' => $this->page['columns'] ));


		// load meta box handling
		add_action( 'admin_enqueue_scripts', function() {

			do_action( 'admin_enqueue_scripts/'.$this->page['menu_slug'] );
			wp_enqueue_script('post');

		});


		if( is_callable( $this->page['metabox'] ) ) {
			add_action('admin_head', array( $this, 'default_meta_box' ) );
		}

		// acf
		$this->acf_admin_load();
	}




	/**
	 * [default_meta_box description]
	 *
	 * @ignore
	 * @since  1.0.0
	 */

	function default_meta_box() {

		add_meta_box('default_meta_box', $this->page['metabox_title'], $this->page['metabox'], 'sfw-admin-page-'.$this->page['menu_slug'], 'normal', 'high');
	}




	/**
	 * [acf_admin_load description]
	 *
	 * @ignore
	 * @since  1.0.0
	 */

	function acf_admin_load() {

		// stop if acf is not allowed
		if( !$this->page['acf'] )
			return;


		// get post_id (allow lang modification)
		$this->page['post_id'] = acf_get_valid_post_id($this->page['post_id']);


		// verify and remove nonce
		if( acf_verify_nonce('options') ) {

			// save data
		   if( acf_validate_save_post(true) ) {

		   	// set autoload
		    acf_update_setting('autoload', $this->page['autoload'] );

		    // save
				acf_save_post( $this->page['post_id'] );

				// redirect
				wp_redirect( add_query_arg(array('message' => '1')) );
				exit;

			}

		}


		// load acf scripts
		acf_enqueue_scripts();

		add_action( 'acf/input/admin_head',					array($this,'acf_admin_head') );
	}




	/**
	 * [acf_admin_head description]
	 *
	 * @ignore
	 * @since  1.0.0
	 */

	function acf_admin_head() {

		// stop if acf is not allowed
		if( !$this->page['acf'] )
			return;


		// get field groups
		$field_groups = acf_get_field_groups(array(
			'sfw_options_page' => $this->page['menu_slug']
		));


		// notices
		if( !empty($_GET['message']) && $_GET['message'] == '1' ) {

			acf_add_admin_notice( $this->page['updated_message'] );

		}



		if( !empty($field_groups) )  {

			// add submit div

			add_meta_box('submitdiv2', __('Save', 'apparelcuts-spreadshirt' ), array($this, 'acf_postbox_submitdiv'), 'sfw-admin-page-'.$this->page['menu_slug'], 'side', 'high');


			foreach( $field_groups as $i => $field_group ) {

				// vars
				$id = "acf-{$field_group['key']}";
				$title = $field_group['title'];
				$context = $field_group['position'];
				$priority = 'high';
				$args = array( 'field_group' => $field_group );


				// tweaks to vars
				if( $context == 'acf_after_title' ) {

					$context = 'normal';

				} elseif( $context == 'side' ) {

					$priority = 'core';

				}


				// add meta box
				add_meta_box( $id, $title, array($this, 'acf_postbox_acf'), 'sfw-admin-page-'.$this->page['menu_slug'], $context, $priority, $args );


			}
			// foreach

		}
		// if

	}




	/**
	 * [acf_postbox_submitdiv description]
	 *
	 * @ignore
	 * @since  1.0.0
	 */

	function acf_postbox_submitdiv( $post, $args ) {

		?>
		<div id="major-publishing-actions">

			<div id="publishing-action">
				<span class="spinner"></span>
				<input type="submit" accesskey="p" value="<?php echo $this->page['update_button']; ?>" class="button button-primary button-large" id="publish" name="publish">
			</div>

			<div class="clear"></div>

		</div>
		<?php

	}




	/**
	 * [acf_postbox_acf description]
	 *
	 * @ignore
	 * @since  1.0.0
	 */

	function acf_postbox_acf( $post, $args ) {

		extract( $args ); // 1
		extract( $args ); // 2


		// vars
		$o = array(
			'id'			=> $id,
			'key'			=> $field_group['key'],
			'style'			=> $field_group['style'],
			'label'			=> $field_group['label_placement'],
			'editLink'		=> '',
			'editTitle'		=> '',
			'visibility'	=> true
		);

		// load fields
		$fields = acf_get_fields( $field_group );

		// render
		acf_render_fields( $fields, $this->page['post_id'], 'div', $field_group['instruction_placement'] );

		?>
		<script type="text/javascript">
		if( typeof acf !== 'undefined' ) {

			acf.newPostbox(<?php echo json_encode($o); ?>);

		}
		</script>
		<?php

	}




	/**
	 * [html description]
	 *
	 * @ignore
	 * @since  1.0.0
	 */

	function html() {

		do_action('sfw_admin_page_add_metaboxes', 'sfw-admin-page-'.$this->page['menu_slug']);

		// load view
		acf_get_view( $this->page['template'], $this->page );

	}

}

