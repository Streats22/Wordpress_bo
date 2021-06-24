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

										 'name' => __('Shirt Designer', 'apparelcuts-spreadshirt' ),
						'singular_name' => __('Shirt Designer', 'apparelcuts-spreadshirt' ),
									'add_new' => _x('Add New', 'product', 'apparelcuts-spreadshirt' ),
						 'add_new_item' => __('Add New Designer', 'apparelcuts-spreadshirt' ),
								'edit_item' => __('Edit Designer', 'apparelcuts-spreadshirt' ),
								 'new_item' => __('New Designer', 'apparelcuts-spreadshirt' ),
								'view_item' => __('View Designer', 'apparelcuts-spreadshirt' ),
						 'search_items' => __('Search Designers', 'apparelcuts-spreadshirt' ),
								'not_found' => __('No Designer found.', 'apparelcuts-spreadshirt' ),
			 'not_found_in_trash' => __('No Designer found in Trash', 'apparelcuts-spreadshirt' ),
				'parent_item_colon' => null,
								'all_items' => __('All Designers', 'apparelcuts-spreadshirt' ),
								 'archives' => null,
				 'insert_into_item' => __('Insert into Designer', 'apparelcuts-spreadshirt' ),
		'uploaded_to_this_item' => __('Uploaded to this Designer', 'apparelcuts-spreadshirt' ),
					 'featured_image' => null,
			 'set_featured_image' => null,
		'remove_featured_image' => null,
			 'use_featured_image' => null,
								'menu_name' => null,
				'filter_items_list' => __('Designer', 'apparelcuts-spreadshirt' ),
		'items_list_navigation' => __('Designer', 'apparelcuts-spreadshirt' ),
							 'items_list' => __('Designer', 'apparelcuts-spreadshirt' ),
					 'name_admin_bar' => _x('Designer', 'Designer menu_name', 'apparelcuts-spreadshirt' ),

	);

	$args = array(
		'labels'     				 => $labels,
		'description'		     => '',
		'public'             => true,
		'publicly_queryable' => true,
		'exclude_from_search'=> true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_position' 		 => sfw_admin_get_posttype_menu_position(),
		'show_in_nav_menus'  => true,
		'query_var'          => true,
		'rewrite' 					 => array(
			'slug' => _x( 'shirt-designer', 'Shirt Designer posttype slug', 'apparelcuts-spreadshirt' )
		),
		'capability_type'    => 'post',
		'map_meta_cap' 			 => true,
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'custom-fields'),
	);


	/**
	 * Filters the confomat posttype before registration
	 *
	 * @see register_post_type
	 * @param array $args
	 * @since 1.0.0
	 */
	$args = apply_filters( 'sfw/register/sfw-confomat', $args );

	register_post_type( 'sfw-confomat', $args );

}, 1  );

