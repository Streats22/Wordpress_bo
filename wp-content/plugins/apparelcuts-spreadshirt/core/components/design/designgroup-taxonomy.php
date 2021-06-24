<?php if ( ! defined( '\ABSPATH' ) ) exit;


add_action( 'init', function(){

	if( !did_action('sfw/init' ) )
		return;


	$labels = array(
												'name' => __('Designgroups', 'apparelcuts-spreadshirt' ),
							 'singular_name' => __('Designgroup', 'apparelcuts-spreadshirt' ),
									 'menu_name' => __('Designgroups', 'apparelcuts-spreadshirt' ),
									 'all_items' => __('All Designgroups', 'apparelcuts-spreadshirt' ),
									 'edit_item' => __('Edit Designgroup', 'apparelcuts-spreadshirt' ),
									 'view_item' => __('View Designgroup', 'apparelcuts-spreadshirt' ),
								 'update_item' => __('Update Designgroup', 'apparelcuts-spreadshirt' ),
								'add_new_item' => __('Add Designgroup', 'apparelcuts-spreadshirt' ),
							 'new_item_name' => __('Add new Designgroup', 'apparelcuts-spreadshirt' ),
								 'parent_item' => __('Parent Designgroup', 'apparelcuts-spreadshirt' ),
					 'parent_item_colon' => __('Parent Designgroup:', 'apparelcuts-spreadshirt' ),
								'search_items' => __('Search Designgroups', 'apparelcuts-spreadshirt' ),
							 'popular_items' => __('Popular Designgroups', 'apparelcuts-spreadshirt' ),
	'separate_items_with_commas' => __('Separate Designgroups with commas', 'apparelcuts-spreadshirt' ),
				 'add_or_remove_items' => __('Add or remove Designgroups', 'apparelcuts-spreadshirt' ),
			 'choose_from_most_used' => __('Choose from most used Designgroups', 'apparelcuts-spreadshirt' ),
									 'not_found' => __('No Designgroups found.', 'apparelcuts-spreadshirt' ),
	);

	$args = array(
		'labels'            => $labels,
		'public' 						=> sfw_is_shop_properly_configured(),
		'hierarchical'      => true,
		'show_ui'           => sfw_is_shop_properly_configured(),
		'show_in_nav_menus' => sfw_is_shop_properly_configured(),
		'show_admin_column' => sfw_is_shop_properly_configured(),
		'query_var'         => true,
		'rewrite'           => array( 'slug' => _x( 'designs', 'Designgroup slug', 'apparelcuts-spreadshirt' ) ),
	);



	/**
	 * Filters the designgroup taxnonomy before registration
	 *
	 * @see register_taxonomy
	 * @param array $args
	 * @since 1.0.0
	 */

	$args = apply_filters('sfw/register/designgroup', $args );

	register_taxonomy( 'sfw-designgroup', array( 'sfw-design' ), $args );

}, 0 );