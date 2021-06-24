<?php if ( ! defined( '\ABSPATH' ) ) exit;




	/**
	* Register the Custom Post Type
	*
	* @since 1.0.0
	*/

	add_action( 'init', function(){

		if( !did_action('sfw/init') )
			return;


		$labels = array(

											 'name' => __('Products', 'apparelcuts-spreadshirt' ),
							'singular_name' => __('Product', 'apparelcuts-spreadshirt' ),
										'add_new' => null,
							 'add_new_item' => null,
									'edit_item' => __('Edit Product', 'apparelcuts-spreadshirt' ),
									 'new_item' => null,
									'view_item' => __('View Product', 'apparelcuts-spreadshirt' ),
							 'search_items' => __('Search Products', 'apparelcuts-spreadshirt' ),
									'not_found' => __('No Products found.', 'apparelcuts-spreadshirt' ),
				 'not_found_in_trash' => __('No Products found in Trash', 'apparelcuts-spreadshirt' ),
					'parent_item_colon' => null,
									'all_items' => __('All Products', 'apparelcuts-spreadshirt' ),
									 'archives' => __('Product Archives', 'apparelcuts-spreadshirt' ),
					 'insert_into_item' => __('Insert into Product', 'apparelcuts-spreadshirt' ),
			'uploaded_to_this_item' => __('Uploaded to this Product', 'apparelcuts-spreadshirt' ),
						 'featured_image' => null,
				 'set_featured_image' => null,
			'remove_featured_image' => null,
				 'use_featured_image' => null,
									'menu_name' => __('Products', 'apparelcuts-spreadshirt' ),
					'filter_items_list' => __('Products', 'apparelcuts-spreadshirt' ),
			'items_list_navigation' => __('Products', 'apparelcuts-spreadshirt' ),
								 'items_list' => __('Products', 'apparelcuts-spreadshirt' ),
						 'name_admin_bar' => __('Product', 'apparelcuts-spreadshirt' ),
		);

		$args = array(
			'labels'     				 => $labels,
			'description'		     => _x( 'Spreadshirt Products', 'Product posttype description', 'apparelcuts-spreadshirt' ),
			'public'             => sfw_is_shop_properly_configured(),
			'publicly_queryable' => sfw_is_shop_properly_configured(),
			'exclude_from_search'=> false,
			'show_ui'            => sfw_is_shop_properly_configured(),
			'show_in_menu'       => sfw_is_shop_properly_configured(),
			'menu_position' 		 => sfw_admin_get_posttype_menu_position(),
			'show_in_nav_menus'  => sfw_is_shop_properly_configured(),
			'query_var'          => true,
			'capability_type'    => 'post',
			'capabilities' 		   => array(
				'create_posts' 		 => false
			 ),
			'map_meta_cap' 			 => true,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'custom-fields'),
			'taxonomies'		 		 => array( 'sfw-productgroup', 'sfw-designid', 'sfw-producttype', 'sfw-brand', 'sfw-printtype', 'sfw-department'),
			'has_archive'        => _x( 'all-products', 'Product Archive Slug', 'apparelcuts-spreadshirt' ),
			'rewrite'            => array( 'slug' => _x( 'product', 'Product Slug', 'apparelcuts-spreadshirt' ) ),
		);


		/**
		 * Filters the product posttype before registration
		 *
		 * @see register_post_type
		 * @param array $args
		 * @since 1.0.0
		 */

		$args = apply_filters( 'sfw/register/sfw-product', $args );


		register_post_type( 'sfw-product', $args );

	}, 1  );

