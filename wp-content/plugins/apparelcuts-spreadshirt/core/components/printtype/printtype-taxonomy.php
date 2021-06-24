<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
* Core Printtype-specific functionality
*
* @since 1.0.0
*/

	/**
	* Register the sfw-printtype Taxonomy
	*
	* @since 1.0.0
	*/
	add_action( 'init', 'sfw_printtype_register', 0 );

	function sfw_printtype_register() {

		if( !did_action('sfw/init' ) )
			return;


		$labels = array(
													'name' => __('Printtypes', 'apparelcuts-spreadshirt' ),
								 'singular_name' => __('Printtype', 'apparelcuts-spreadshirt' ),
										 'menu_name' => _x('Printtypes', 'menu_name', 'apparelcuts-spreadshirt' ),
										 'all_items' => __('All Printtypes', 'apparelcuts-spreadshirt' ),
										 'edit_item' => __('Edit Printtype', 'apparelcuts-spreadshirt' ),
										 'view_item' => __('View Printtype', 'apparelcuts-spreadshirt' ),
									 'update_item' => __('Update Printtype', 'apparelcuts-spreadshirt' ),
									'add_new_item' => null,
								 'new_item_name' => null,
									 'parent_item' => null,
						 'parent_item_colon' => null,
									'search_items' => __('Search Printtypes', 'apparelcuts-spreadshirt' ),
								 'popular_items' => null,
		'separate_items_with_commas' => null,
					 'add_or_remove_items' => null,
				 'choose_from_most_used' => null,
										 'not_found' => __('No Printtypes found.', 'apparelcuts-spreadshirt' ),
		);


		$args = array(
			'labels'            	=> $labels,
			'public' 							=> true,
			'hierarchical'      	=> false,
			'show_ui'           	=> true,
			'show_in_nav_menus' 	=> false,
			'show_in_quick_edit' 	=> false,
			'show_admin_column' 	=> false,
			'query_var'         	=> true,
			'rewrite'           	=> array( 'slug' => _x('printtype', 'Printtype slug', 'apparelcuts-spreadshirt' ) ),
		);


		/**
		 * Filters the printtype taxnonomy before registration
		 *
		 * @see register_taxonomy
		 * @param array $args
		 * @since 1.0.0
		 */

		$args = apply_filters('sfw/register/sfw-printtype', $args );

		register_taxonomy( 'sfw-printtype', array( 'sfw-product' ), $args );

	}




/*
* add the Spreadshirt Id to the edit-form
*/

add_action( 'sfw-printtype_edit_form_fields', 'sfw_edit_form_field_spreadshirt_id' );




/*
* hide add new form
*/

add_action( 'after-sfw-printtype-table', 'sfw_hide_add_new_form_css' );




/*
* add helpful links to Term Edit Screen
*/

add_action( 'sfw-printtype_edit_form_fields', function() {

	$links = array(
		sfw_get_printtype_resource_link()
	);

	sfw_print_term_edit_form_field_row( implode( $links, '<br/>' ), __( 'Helpful Links', 'apparelcuts-spreadshirt' ));

} );




/*
* add row actions
*/

add_filter( 'sfw-printtype_row_actions', 	function( $actions, $term ) {

	$printtype_id = sfw_get_spreadshirt_id_by_term( $term );
	$actions[] = sfw_get_printtype_resource_link( $printtype_id );

	return $actions;

}, 10, 2 );




/*
* Remove Metabox
*/

sfw_remove_meta_box_for_taxonomy( 'sfw-printtype' );







