<?php if ( ! defined( '\ABSPATH' ) ) exit;



// @see wp-includes/template.php




// single post types
add_filter( 'single_template', function( $template, $type, $templates ){

  $sfw_templates = array( 'single-sfw-product.php', 'single-sfw-design.php', 'single-sfw-confomat.php' );

  foreach( $sfw_templates as $sfw_template ) {
    $t = sfw_maybe_get_better_template( $sfw_template, $template, $templates );
    if( $template != $t )
      return $t;
  }

  return $template;

}, 20, 3 );




// post types archives
add_filter( 'archive_template', function( $template, $type, $templates ){

  $sfw_templates = array(
    'archive-sfw-product.php',
    'archive-sfw-design.php'
  );

  foreach( $sfw_templates as $sfw_template ) {
    $t = sfw_maybe_get_better_template( $sfw_template, $template, $templates );

    if( $template != $t )
      return $t;
  }

  return $template;

}, 20, 3 );




// post types archives
add_filter( 'taxonomy_template', function( $template, $type, $templates ){

  $sfw_templates = array(
    'taxonomy-sfw-producttype.php',
    'taxonomy-sfw-productgroup.php',
    'taxonomy-sfw-brand.php',
    'taxonomy-sfw-printtype.php',
    'taxonomy-sfw-designtag.php',
    'taxonomy-sfw-designgroup.php'
  );

  if( sfw_is_pro() ) {
    $sfw_templates[] = 'taxonomy-sfw-department.php';
  }

  foreach( $sfw_templates as $sfw_template ) {

    $t = sfw_maybe_get_better_template( $sfw_template, $template, $templates );
    if( $template != $t )
      return $t;
  }

  return $template;

}, 20, 3 );




/* working code if a functions.php inside theme compat becomes nescessary at some point :

function sfw_maybe_include_theme_compat_functions_php() {

  if( is_admin() )
    return;

  $file = sfw_maybe_get_theme_compat_file( 'functions.php' );

  if( $file ){
    @include_once( $file );
  }
}

add_action( 'after_setup_theme', 'sfw_maybe_include_theme_compat_functions_php');

*/