<?php if ( ! defined( '\ABSPATH' ) ) exit;



add_action( 'init', function(){

	if( !did_action('sfw/init' ) )
		return;


	$labels = array(
												'name' => __('Design-Tags', 'apparelcuts-spreadshirt' ),
							 'singular_name' => __('Design-Tag', 'apparelcuts-spreadshirt' ),
									 'menu_name' => __('Design-Tags', 'apparelcuts-spreadshirt' ),
									 'all_items' => __('All Design-Tags', 'apparelcuts-spreadshirt' ),
									 'edit_item' => __('Edit Design-Tag', 'apparelcuts-spreadshirt' ),
									 'view_item' => __('View Design-Tag', 'apparelcuts-spreadshirt' ),
								 'update_item' => __('Update Design-Tag', 'apparelcuts-spreadshirt' ),
								'add_new_item' => __('Add Design-Tag', 'apparelcuts-spreadshirt' ),
							 'new_item_name' => __('Add new Design-Tag', 'apparelcuts-spreadshirt' ),
								 'parent_item' => null,
					 'parent_item_colon' => null,
								'search_items' => __('Search Design-Tags', 'apparelcuts-spreadshirt' ),
							 'popular_items' => __('Popular Design-Tags', 'apparelcuts-spreadshirt' ),
	'separate_items_with_commas' => __('Separate Design-Tags with commas', 'apparelcuts-spreadshirt' ),
				 'add_or_remove_items' => __('Add or remove Design-Tags', 'apparelcuts-spreadshirt' ),
			 'choose_from_most_used' => __('Choose from most used Design-Tags', 'apparelcuts-spreadshirt' ),
									 'not_found' => __('No Design-Tags found.', 'apparelcuts-spreadshirt' ),
	);


	$args = array(
		'labels'            => $labels,
		'public' 						=> sfw_is_shop_properly_configured(),
		'hierarchical'      => false,
		'show_ui'           => sfw_is_shop_properly_configured(),
		'show_in_nav_menus' => sfw_is_shop_properly_configured(),
		'show_admin_column' => sfw_is_shop_properly_configured(),
		'query_var'         => true,
		'rewrite'           => array( 'slug' => _x( 'tagged', 'Designtag slug', 'apparelcuts-spreadshirt' ) ),
	);

	/**
	 * Filters the designtag taxnonomy before registration
	 *
	 * @see register_taxonomy
	 * @param array $args
	 * @since 1.0.0
	 */

	$args = apply_filters('sfw/register/designtag', $args );


	register_taxonomy( 'sfw-designtag', array( 'sfw-design' ), $args );


}, 0 );




