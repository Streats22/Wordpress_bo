<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Taxonomy Screen Helper functions
 *
 * Functions that modify the term edit form or term list table
 *
 * @since 1.0.0
 */





/**
* Prints Table row for the table in the term edit form
*
* @param string $right
* @param string $left
*
* @since 1.0.0
*/

function sfw_print_term_edit_form_field_row( $right = '', $left = '' ) {

  ?>
	<tr class="form-field">
		<th scope="row"><?php echo $left; ?></th>
		<td>
      <?php echo $right; ?>
    </td>
	</tr>
  <?php
}




/**
* Add Spreadshirt Id to form edit screen
*
* @see HOOK {$taxonomy}_edit_form_fields
*
* @since 1.0.0
*/

function sfw_edit_form_field_spreadshirt_id( $term ) {

  if( empty( $spreadshirt_id = sfw_get_spreadshirt_id_by_term( $term ) ) )
    return;

  sfw_print_term_edit_form_field_row( $spreadshirt_id, '<label for="spreadshirt-id">'.__( 'Spreadshirt-Id', 'apparelcuts-spreadshirt' ).'</label>' );
}




/**
* Create new Column for Spreadshirt Ids in Term WP list table
*
* @see HOOK manage_edit-{$taxonomy}_columns
*
* @since 1.0.0
*/

function sfw_add_column_spreadshirt_id( $columns ) {

  $columns['spreadshirt-id'] = __('Spreadshirt-Id', 'apparelcuts-spreadshirt' );
  return $columns;
}




/**
* Fill Spreadshirt Id column in WP Term list table
*
* @see HOOK manage_-{$taxonomy}_custom_column
*
* @since 1.0.0
*/

function sfw_add_custom_column_spreadshirt_id( $none, $column_name, $term_id ) {

  if ( 'spreadshirt-id' == $column_name ) {

		$spreadshirt_id = sfw_get_spreadshirt_id_by_term( $term_id );

    if( !empty( $spreadshirt_id ) )
      echo $spreadshirt_id;
    else {
      echo '-';
    }
  }
}




/**
* Create new Column for Previews in Term WP list table
*
* @see HOOK manage_edit-{$taxonomy}_columns
*
* @since 1.0.0
*/

function sfw_add_column_preview( $columns ) {

  $columns['sfw-preview'] = __('Preview', 'apparelcuts-spreadshirt' );
  return $columns;
}




/**
* Hides the add new term form
*
* @see HOOK after-{$taxonomy}-table
* @todo check if wordpress adds a 'add_new_term' capabiltiy in the future
*
* @since 1.0.0
*/

function sfw_hide_add_new_form_css() {

  ?>
  <style>
    #col-left {
      display:none!IMPORTANT;
      opacity: hidden!IMPORTANT;
      position: relative!IMPORTANT;
      right:-100000px!IMPORTANT;
    }
    #col-right {
      float:none!IMPORTANT;
      width:auto!IMPORTANT;
    }
  </style>
  <?php
}




/**
* Remove the meta_box for a taxonomy
*
* @since 1.0.0
*/

function sfw_remove_meta_box_for_taxonomy( $taxonomy, $object = 'sfw-product', $position = 'side' ) {

  if( !is_admin() )
    return;

  add_action( 'admin_menu', function ( ) use ( $taxonomy, $object, $position ) {

    $selector = is_taxonomy_hierarchical( $taxonomy ) ? $taxonomy.'div' : 'tagsdiv-'.$taxonomy;

    remove_meta_box( $selector, $object, $position );

  } );
}







