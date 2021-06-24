<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
* Register the sfw-producttype Taxonomy
*
* @since 1.0.0
*/
add_action( 'init', 'sfw_producttypeid_register', 0 );

function sfw_producttypeid_register() {

	if( !did_action('sfw/init' ) )
		return;

	$args = array(
		'labels' => array(
									  				'name' => __('Producttypes', 'apparelcuts-spreadshirt' ),
								   'singular_name' => __('Producttype', 'apparelcuts-spreadshirt' ),
								 		   'menu_name' => __('Producttypes', 'apparelcuts-spreadshirt' ),
										   'all_items' => __('All Producttypes', 'apparelcuts-spreadshirt' ),
										   'edit_item' => __('Edit Producttype', 'apparelcuts-spreadshirt' ),
										   'view_item' => __('View Producttype', 'apparelcuts-spreadshirt' ),
									   'update_item' => __('Update Producttype', 'apparelcuts-spreadshirt' ),
									  'add_new_item' => null,
								   'new_item_name' => null,
								     'parent_item' => null,
						   'parent_item_colon' => null,
						 		    'search_items' => __('Search Producttypes', 'apparelcuts-spreadshirt' ),
								 	 'popular_items' => null,
			'separate_items_with_commas' => null,
						 'add_or_remove_items' => null,
					 'choose_from_most_used' => null,
					 						 'not_found' => __('No Producttypes found.', 'apparelcuts-spreadshirt' ),
		),
		'public' 						  => sfw_is_shop_properly_configured(),
		'hierarchical'      	=> false,
		'show_ui'           	=> sfw_is_shop_properly_configured(),
		'show_in_nav_menus' 	=> sfw_is_shop_properly_configured(),
		'show_in_quick_edit' 	=> false,
		'show_admin_column' 	=> sfw_is_shop_properly_configured(),
		'query_var'         	=> true,
		'update_count_callback' => function( $tt_ids, $taxonomy ) {

			// Only post types are attached to this taxonomy
			_update_post_term_count( $tt_ids, $taxonomy );

			do_action( 'sfw/sfw-producttype/updated_count', $tt_ids, $taxonomy );

		},
		'rewrite'           	=> array( 'slug' => _x( 'producttype', 'Producttype slug', 'apparelcuts-spreadshirt' ) ),
	);



	/**
	 * Filters the producttype taxnonomy before registration
	 *
	 * @see register_taxonomy
	 * @param array $args
	 * @since 1.0.0
	 */

	$args = apply_filters('sfw/register/sfw-producttype', $args );


	register_taxonomy( 'sfw-producttype', array( 'sfw-product' ), $args );

}




/*
* add the Spreadshirt Id to the Edit Term Screen
*/

add_action( 'sfw-producttype_edit_form_fields', 'sfw_edit_form_field_spreadshirt_id' );




/*
* add a Preview to the Edit Term Screen
*/

add_action( 'sfw-producttype_edit_form_fields', function( $term ) {

	sfw_print_term_edit_form_field_row( @sfw_producttype_image()->img() );
} );




/*
* hide add new form
*/

add_action( 'after-sfw-producttype-table', 'sfw_hide_add_new_form_css' );




/*
* add Spreadshirt Id Column
*/

sfw_add_taxonomy_table_column( 'sfw-producttype', __( 'Spreadshirt ID', 'apparelcuts-spreadshirt' ), function( $term_id ){

	return sfw_get_producttype_id( get_term( $term_id ) );

});




/*
* add Preview Column
*/

sfw_add_taxonomy_table_column( 'sfw-producttype', __( 'Preview', 'apparelcuts-spreadshirt' ), function( $term_id ){

	$producttype_id = sfw_get_spreadshirt_id_by_item( 'producttype', get_term( $term_id ) );
	sfw_producttype_image( array( 100, 100 ), $producttype_id )->img();

});




/*
* add helpful links to Term Edit Screen
*/

add_action( 'sfw-producttype_edit_form_fields', function() {

	$links = array(
		sfw_get_producttype_resource_link(),
		sfw_get_producttype_platform_link()
	);

	sfw_print_term_edit_form_field_row( implode( $links, '<br/>' ), __( 'Helpful Links', 'apparelcuts-spreadshirt' ));
} );




/*
* add row actions
*/

add_filter( 'sfw-producttype_row_actions', 	function( $actions, $term ) {

	$producttype_id = sfw_get_spreadshirt_id_by_term( $term );
	$actions[] = sfw_get_producttype_resource_link( $producttype_id );
	$actions[] = sfw_get_producttype_platform_link( $producttype_id );

	return $actions;

}, 10, 2 );




/*
* Remove Metabox
*/

sfw_remove_meta_box_for_taxonomy( 'sfw-producttype' );






