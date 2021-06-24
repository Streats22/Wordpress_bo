<?php if ( ! defined( '\ABSPATH' ) ) exit;



	/**
	* Register the sfw-producttype Taxonomy
	*
	* @since 1.0.0
	*/
	add_action( 'init', 'sfw_brand_register', 0 );

	function sfw_brand_register() {

		if( !did_action('sfw/init' ) )
			return;

		$labels = array(
													'name' => __('Brands', 'apparelcuts-spreadshirt' ),
								 'singular_name' => __('Brand', 'apparelcuts-spreadshirt' ),
										 'menu_name' => __('Brands', 'apparelcuts-spreadshirt' ),
										 'all_items' => __('All Brands', 'apparelcuts-spreadshirt' ),
										 'edit_item' => __('Edit Brand', 'apparelcuts-spreadshirt' ),
										 'view_item' => __('View Brand', 'apparelcuts-spreadshirt' ),
									 'update_item' => __('Update Brand', 'apparelcuts-spreadshirt' ),
									'add_new_item' => null,
								 'new_item_name' => null,
									 'parent_item' => null,
						 'parent_item_colon' => null,
									'search_items' => __('Search Brands', 'apparelcuts-spreadshirt' ),
								 'popular_items' => __('Popular Brands', 'apparelcuts-spreadshirt' ),
		'separate_items_with_commas' => null,
					 'add_or_remove_items' => null,
				 'choose_from_most_used' => null,
										 'not_found' => __('No Brands found.', 'apparelcuts-spreadshirt' ),
		);

		$args = array(
			'labels'            	=> $labels,
			'public' 							=> true,
			'hierarchical'      	=> false,
			'show_ui'           	=> true,
			'show_in_nav_menus' 	=> true,
			'show_in_quick_edit' 	=> false,
			'show_admin_column' 	=> false,
			'query_var'         	=> true,
			'rewrite'           	=> array( 'slug' => _x( 'brand', 'The brand slug', 'apparelcuts-spreadshirt' ) ),
		);


		/**
		 * Filters the brand taxnonomy before registration
		 *
		 * @see register_taxonomy
		 * @param array $args
		 * @since 1.0.0
		 */

		$args = apply_filters('sfw/register/sfw-brand', $args );

		register_taxonomy( 'sfw-brand', array( 'sfw-product' ), $args );
	}




	/*
	* hide add new form
	*/

	add_action( 'after-sfw-brand-table', 'sfw_hide_add_new_form_css' );




	/*
	* Remove Metabox
	*/

	sfw_remove_meta_box_for_taxonomy( 'sfw-brand' );


