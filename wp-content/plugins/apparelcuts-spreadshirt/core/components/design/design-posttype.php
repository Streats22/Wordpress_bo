<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
* handle connection of designs
*
*/


/**
* Register the Custom Post Type
*
* @since 1.0.0
*/

add_action( 'init', function() {

	if( !did_action('sfw/init') )
		return;

	$labels = array(

										 'name' => __('Designs', 'apparelcuts-spreadshirt' ),
						'singular_name' => __('Design', 'apparelcuts-spreadshirt' ),
									'add_new' => null,
						 'add_new_item' => null,
								'edit_item' => __('Edit Design', 'apparelcuts-spreadshirt' ),
								 'new_item' => null,
								'view_item' => __('View Design', 'apparelcuts-spreadshirt' ),
						 'search_items' => __('Search Designs', 'apparelcuts-spreadshirt' ),
								'not_found' => __('No Designs found.', 'apparelcuts-spreadshirt' ),
			 'not_found_in_trash' => __('No Designs found in Trash', 'apparelcuts-spreadshirt' ),
				'parent_item_colon' => null,
								'all_items' => __('All Designs', 'apparelcuts-spreadshirt' ),
								 'archives' => __('Design Archives', 'apparelcuts-spreadshirt' ),
				 'insert_into_item' => __('Insert into Design', 'apparelcuts-spreadshirt' ),
		'uploaded_to_this_item' => __('Uploaded to this Design', 'apparelcuts-spreadshirt' ),
					 'featured_image' => null, //_x('Featured Image', 'design', 'apparelcuts-spreadshirt' ),
			 'set_featured_image' => null, //_x('Set featured Image', 'design', 'apparelcuts-spreadshirt' ),
		'remove_featured_image' => null, //_x('Remove featured Image', 'design', 'apparelcuts-spreadshirt' ),
			 'use_featured_image' => null, //_x('Use as featured Image', 'design', 'apparelcuts-spreadshirt' ),
								'menu_name' => _x('Designs', 'menu_name', 'apparelcuts-spreadshirt' ),
				'filter_items_list' => __('Designs', 'apparelcuts-spreadshirt' ),
		'items_list_navigation' => __('Designs', 'apparelcuts-spreadshirt' ),
							 'items_list' => __('Designs', 'apparelcuts-spreadshirt' ),
					 'name_admin_bar' => _x('Designs', 'menu_name', 'apparelcuts-spreadshirt' ),
	);

	$args = array(
		'labels'     					=> $labels,
		'description'		 			=> _x('Spreadshirt Designs', 'posttype description', 'apparelcuts-spreadshirt' ),
		'public'             	=> sfw_is_shop_properly_configured(),
		'publicly_queryable' 	=> sfw_is_shop_properly_configured(),
		'exclude_from_search'	=> false,
		'show_ui'            	=> sfw_is_shop_properly_configured(),
		'show_in_menu'       	=> sfw_is_shop_properly_configured(),
		'menu_position' 		 	=> sfw_admin_get_posttype_menu_position(),
		'show_in_nav_menus'  	=> sfw_is_shop_properly_configured(),
		'query_var'          	=> true,
		'capability_type'    	=> 'post',
		'capabilities' 				=> array(
			'create_posts' => false,
		),
		'map_meta_cap' 				=> true,
		'has_archive'        	=> true,
		'hierarchical'       	=> false,
		'supports'           	=> array( 'title', 'editor' ),
		'taxonomies'		 			=> array('sfw-designgroup', 'sfw-designtag' ),
		'has_archive'        	=> _x( 'all-designs', 'Design Archive Slug', 'apparelcuts-spreadshirt' ),
		'rewrite'            	=> array( 'slug' => _x( 'design', 'design posttype slug', 'apparelcuts-spreadshirt' ) ),
	);

	/**
	 * Filters the design posttype before registration
	 *
	 * @see register_post_type
	 * @param array $args
	 * @since 1.0.0
	 */
	$args = apply_filters('sfw/register/sfw-design', $args );

	register_post_type( 'sfw-design', $args );

}, 1 );




