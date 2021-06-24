<?php if ( ! defined( '\ABSPATH' ) ) exit;




/*
 * Metabox with embed code
 */

sfw_add_meta_box(array(
  'id' => 'sfw-confomat-shortcode',
  'title' => __( 'Shortcode', 'apparelcuts-spreadshirt' ),
  'screen' => 'sfw-confomat',
  'context' => 'side',
  'callback' => function(){

    if( $id = get_the_ID() ) {

      echo '<p>', __('Use the following code to embed this T-Shirt Designer into other posts', 'sfw ');
      printf('<input class="short-code-suggestion" onclick="jQuery && jQuery(this).select();" type="text" value="%s"/>', esc_attr( '[sfw confomat="'.$id.'"]') );
    }
  }
));




/*
 * Fill producttype select
 */

add_filter('acf/load_field/key=field_producttypeid', function( $field ){

  if( !is_admin() ||  sfw_is_acf_group_edit_screen() )
    return $field;

  $producttypes = sfw_get_producttypes_minified( );
  $choices = array();

  if( is_spreadshirt_object( $producttypes ) )
    foreach( $producttypes->productTypes as $producttype ) {
      $choices[$producttype->id] = $producttype->name;
    }

  $field['choices'] = $choices;

  return $field;
});




/*
 * Fill appearance select
 */

add_filter('acf/load_field/key=field_appearanceid', function( $field ){

  if( !is_admin() || sfw_is_acf_group_edit_screen() )
    return $field;

  $producttypes = sfw_get_producttypes_minified( );
  $choices = array();

  if( is_spreadshirt_object( $producttypes ) )
    foreach( $producttypes->productTypes as $producttype ) {
      foreach( $producttype->appearances as $appearance ) {
        $choices[ $appearance->id ] = sprintf( '%s (ID:%s)', $appearance->name, $appearance->id );
      }
    }

  $field['choices'] = $choices;

  return $field;
});




/*
 * Fill view select
 */

add_filter('acf/load_field/key=field_viewid', function( $field ){

  if( !is_admin() || sfw_is_acf_group_edit_screen() )
    return $field;

  $producttypes = sfw_get_producttypes_minified( );
  $choices = array();

  if( is_spreadshirt_object( $producttypes ) )
    foreach( $producttypes->productTypes as $producttype ) {
      foreach( $producttype->views as $view ) {
        $choices[ $view->id ] = sprintf( '%s (ID:%s)', $view->name, $view->id );
      }
    }

  $field['choices'] = $choices;

  return $field;
});




/*
 * Fill size select
 */

add_filter('acf/load_field/key=field_sizeid', function( $field ){

  if( !is_admin() || sfw_is_acf_group_edit_screen() )
    return $field;

  $producttypes = sfw_get_producttypes_minified( );
  $choices = array();

  if( is_spreadshirt_object( $producttypes ) )
    foreach( $producttypes->productTypes as $producttype ) {
      foreach( $producttype->sizes as $size ) {
        $choices[ $size->id ] = sprintf( '%s (ID:%s)', $size->name, $size->id );
      }
    }

  $field['choices'] = $choices;

  return $field;
});




/*
 * Fill departmennt select
 */

add_filter('acf/load_field/key=field_departmentid', function( $field ){

  if( !is_admin() || sfw_is_acf_group_edit_screen() )
    return $field;

  $departments = sfw_get_departments( );
  $choices = array();

  if( is_spreadshirt_object( $departments ) )
    foreach( $departments->productTypeDepartments as $department ) {
      $choices[ $department->id ] = sprintf( '%s (ID:%s)', $department->name, $department->id );
    }

  $field['choices'] = $choices;

  return $field;
});




/*
 * Fill producttype category select 
 */

add_filter('acf/load_field/key=field_producttypecategoryid', function( $field ){

  if( !is_admin() || sfw_is_acf_group_edit_screen() )
    return $field;

  $departments = sfw_get_departments( );
  $choices = array();

  if( is_spreadshirt_object( $departments ) )
    foreach( $departments->productTypeDepartments as $department ) {
      foreach( $department->categories as $category ) {

        $choices[ $category->id ] = sprintf( '%s > %s (ID:%s)', $department->name, $category->name, $category->id );
      }
    }

  $field['choices'] = $choices;

  return $field;
});