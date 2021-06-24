<?php if ( ! defined( '\ABSPATH' ) ) exit;


add_action( 'init', function(){

	if( !did_action('sfw/init' ) )
		return;

	$args = array(
		'labels'            => array(
								  				'name' => __('Productgroups', 'apparelcuts-spreadshirt' ),
							   'singular_name' => __('Productgroup', 'apparelcuts-spreadshirt' ),
							 		   'menu_name' => __('Productgroups', 'apparelcuts-spreadshirt' ),
									   'all_items' => __('All Productgroups', 'apparelcuts-spreadshirt' ),
									   'edit_item' => __('Edit Productgroup', 'apparelcuts-spreadshirt' ),
									   'view_item' => __('View Productgroup', 'apparelcuts-spreadshirt' ),
								   'update_item' => __('Update Productgroup', 'apparelcuts-spreadshirt' ),
								  'add_new_item' => __('Add Productgroup', 'apparelcuts-spreadshirt' ),
							   'new_item_name' => __('Add new Productgroup', 'apparelcuts-spreadshirt' ),
							     'parent_item' => __('Parent Productgroup', 'apparelcuts-spreadshirt' ),
					   'parent_item_colon' => __('Parent Productgroup:', 'apparelcuts-spreadshirt' ),
					 		    'search_items' => __('Search Productgroups', 'apparelcuts-spreadshirt' ),
							 	 'popular_items' => __('Popular Productgroups', 'apparelcuts-spreadshirt' ),
		'separate_items_with_commas' => __('Separate Productgroups with commas', 'apparelcuts-spreadshirt' ),
					 'add_or_remove_items' => __('Add or remove Productgroups', 'apparelcuts-spreadshirt' ),
				 'choose_from_most_used' => __('Choose from most used Productgroups', 'apparelcuts-spreadshirt' ),
				 						 'not_found' => __('No Productgroups found.', 'apparelcuts-spreadshirt' ),
		),
		'public' 				      => sfw_is_shop_properly_configured(),
		'hierarchical'      	=> true,
		'show_ui'           	=> sfw_is_shop_properly_configured(),
		'show_in_nav_menus' 	=> sfw_is_shop_properly_configured(),
		'show_in_quick_edit' 	=> sfw_is_shop_properly_configured(),
		'show_admin_column' 	=> sfw_is_shop_properly_configured(),
		'query_var'         	=> true,
		'rewrite'           	=> array( 'slug' => _x( 'products', 'Productgroup slug', 'apparelcuts-spreadshirt' ) ),
	);


	/**
	 * Filters the productgroup taxnonomy before registration
	 *
	 * @see register_taxonomy
	 * @param array $args
	 * @since 1.0.0
	 */

	$args = apply_filters('sfw/register/sfw-productgroup', $args );


	register_taxonomy( 	'sfw-productgroup', array( 'sfw-product' ), $args );

}, 0 );
