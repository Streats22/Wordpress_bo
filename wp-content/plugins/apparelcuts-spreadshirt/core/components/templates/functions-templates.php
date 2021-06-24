<?php if ( ! defined( '\ABSPATH' ) ) exit;





/**
* Retrieve the Template Directory path
*
* @param string optional a file or path to append
* @return string template path
* @since 1.0.0
*/

function sfw_template_path( $file = '' ) {

  return sfw_path( 'template/'.$file );
}




/**
* Retrieve the Template Directory Url
*
* @param string optional a file or path to append
* @return string template url
* @since 1.0.0
*/

function sfw_template_url( $file = '' ) {

  return sfw_url( 'template/'.$file );
}




/**
* return full file path for template file, checks for special theme compatibility
*
* @param string a file or path
* @return string
* @since 1.0.0
*/

function sfw_locate_template( $file, $theme_compat = true ) {


  // check if the file exists in themes
  $maybe_file = locate_template( $file );


  if( !empty( $maybe_file ) )
    return $maybe_file;


  // check if file exists as theme compat file
  if( $theme_compat )
    if( $maybe_file = sfw_maybe_get_theme_compat_file( $file ) )
      return $maybe_file;



  // get sfw template file
  $maybe_file = sfw_template_path( $file );


  if( file_exists( $maybe_file ) )
    return $maybe_file;


  return '';
}




/**
 * checks if theme compatibility file exists
 *
 * @param  string $file
 * @return string|false
 * @since  1.0.0
 */

function sfw_maybe_get_theme_compat_file( $file ) {

  // check for special theme compat file
  $folder_name = get_stylesheet();
  $theme_template_path = sfw_template_path( 'theme_compat/'.$folder_name  ).'/';
  $maybe_file = $theme_template_path.$file;
  // a folder that matches the theme exists inside the template directory
  if( file_exists( $maybe_file ) ){
    return $maybe_file;
  }

  return false;
}




/**
* Include a Template File, return Output
*
* @uses ob_start
* @uses ob_get_contents
*
* @param string a file or path to include
* @return string template output as string
* @since 1.0.0
*/

function sfw_get_template_contents( $file ) {

  $template = sfw_locate_template( $file );

  if( empty( $template ) )
    return '';

  ob_start();
  require( $template );
  $content = ob_get_contents();
  ob_end_clean();

  return $content;
}




/**
* Include a Template File
*
* @param string a file or path to include
* @since 1.0.0
*/

function sfw_include_template( $file, $theme_compat = true ) {

  $file = apply_filters( 'sfw/include_template', $file );

  $template = sfw_locate_template( $file, $theme_compat );

  if( file_exists( $template ) ) {

    require( $template );
  }
  else {
    sfw_debug( 'Unable to include Template '.$file );
  }
}




/**
 * Checks if a integrated template has a higher priority than a choosen template
 *
 * @param  string $sfw_template A file inside of this plugins template folder, the name must match one of $templates
 * @param  string $template An absolute path to a template file, returned by locate_template
 * @param  array $templates A list of possilbe template files
 *
 * @return string An absolute path to a template file
 *
 * @since  1.0.0
 */

function sfw_maybe_get_better_template( $sfw_template, $template, $templates ) {


  if( !in_array( $sfw_template, $templates ) )
    return $template;

  if( empty( $template ) ) {
    $file = sfw_locate_template( $sfw_template );
    if( !empty( $file ) )
      return $file;
    else
      return $template;
  }

  $templates = array_values( $templates );

  $sfw_template_priority = array_search( $sfw_template, $templates );

  $template_priority = array_search( basename( $template ), $templates );


  // if sfw template has a higher priority than the automatically choosen template
  if( $template_priority > $sfw_template_priority ) {

    $file = sfw_locate_template( $sfw_template );
    if( !empty( $file ) )
      return $file;
  }

  return $template;
}