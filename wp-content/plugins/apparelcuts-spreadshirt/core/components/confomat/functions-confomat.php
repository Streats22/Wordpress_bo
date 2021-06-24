<?php if ( ! defined( '\ABSPATH' ) ) exit;





/**
* Retieve the ID of the page which should display the tablomat app
*
* @return int|false
* @since 1.0.0
*/

function sfw_get_confomat_page_id() {

  return sfw_get_page_id( 'confomat' );
}




/**
* Check if a confomat id is specified and post exists and is published
*
* @return bool
* @since 1.0.0
*/

function sfw_confomat_exists() : bool {

  if( false === $post_id = sfw_get_confomat_page_id() )
    return false;

  return get_post_status( $post_id ) === 'publish';
}




/**
* Check if the currents page layout displays a confomat and matches the main confomats page id
*
* @return bool
* @since 1.0.0
*/

function sfw_is_the_confomat( $post_id = false ) : bool {

  return sfw_is_page( 'confomat', $post_id );
}




/**
* Retieve the permalink for the confomat page.
*
* It can build a hash query to open a specific configuration.
*
* @param path - a associative Array with guilty Confomat Params like array( 'I' => '123456' )
* @see CONFOMAT Class, core/types/page.confomat.php
* @since 1.0.0
*/

function sfw_get_customize_link( $path =array() ) {

  $link = sfw_get_page_link( 'confomat' );

  // build url from path
  if( $link && !empty( $path ) ) {

    // @todo change to new param structure
    // first validate all values, unset non existing keys
    array_filter( $path, function( $value, $key ) {
        return !empty( $value ) && isset( sfw_get_possible_confomat_params()[$key] );
    }, ARRAY_FILTER_USE_BOTH );

    // still anything to do?
    if( !empty( $path )) {
      array_walk( $path, function( &$value, $key ) {
        $value = $key.$value;
      });

      $link .= '#!'.implode( '!', $path );
    }
  }

  return $link;
}

function sfw_customize_link( $path =array() ) {

  echo sfw_get_customize_link( $path );
}




/**
* Retrieve the customize link for the current design
*
* @see sfw_get_customize_link
* @param array $path - this is optional to extend the path
* @return false|string
* @since 1.0.0
*/

function sfw_get_design_customize_link( $path = array() ) {

  return sfw_get_customize_link( wp_parse_args( array(
    'I' => sfw_get_design_id()
  )));
}




/**
 * Echoes the design customize link
 *
 * @param  array $path
 * @return string
 * @since  1.0.0
 */

function sfw_design_customize_link( $path = array() ) {

  echo sfw_get_design_customize_link( $path );
}






/**
* retrieve the customize link for the current product
*
* @param array $path - this is optional to extend the path
* @return false|string
* @uses sfw_get_customize_link
* @since 1.0.0
*/

function sfw_get_product_customize_link( $path = array() ) {

  return sfw_get_customize_link( wp_parse_args( array(
    'P' => sfw_get_product_id()
  )));
}




/**
 * Echoes the product customize link
 *
 * @see sfw_get_product_customize_link
 * @param  array $path
 * @return string
 * @since  1.0.0
 */

function sfw_product_customize_link( $path = array() ) {

  echo sfw_get_product_customize_link( $path );
}






/**
* Retrieve the customize link for the current article
*
* @see sfw_get_product_customize_link
* @param array $path - this is optional to extend the path
* @return false|string
* @since 1.0.0
*/

function sfw_get_article_customize_link( $path = array() ) {

  return sfw_get_product_customize_link( $path );
}




/**
 * Echoes the article customize link
 *
 * @see sfw_get_product_customize_link
 * @param  array $path
 * @return string
 * @since  1.0.0
 */

function sfw_article_customize_link( $path = array() ) {

  echo sfw_get_product_customize_link( $path );
}




/**
* Retrieve the customize link for the current producttype
*
* @see sfw_get_customize_link
* @param array $path - this is optional to extend the path
* @return false|string
* @since 1.0.0
*/

function sfw_get_producttype_customize_link( $path = array() ) {

  return sfw_get_customize_link( wp_parse_args( array(
    'T' => sfw_get_producttype_id()
  )));
}




/**
 * Echoes the customize link for the current producttype
 *
 * @see sfw_get_producttype_customize_link
 * @param  array $path
 * @return string
 * @since  1.0.0
 */

function sfw_producttype_customize_link( $path = array() ) {

  echo sfw_get_producttype_customize_link( $path );
}




/**
 * Retrieve List of Confomat Params
 *
 * @return array
 * @since  1.0.0
 */

function sfw_get_possible_confomat_params() {

  /**
   * Filters the list of possible confomat params
   *
   * @param array $list
   */

	return apply_filters( 'sfw/confomat_param_list', array(

  		//-designUrl : 'UPLOAD',

      // --- Design

      array(
        'short_param' => 'I',
        'param' => 'designId',
      ),

      array(
        'short_param' => 'DC1',
        'param' => 'designColor1',
      ),

      array(
        'short_param' => 'DC2',
        'param' => 'designColor2',
      ),

      array(
        'short_param' => 'DC3',
        'param' => 'designColor3',
      ),

      array(
        'short_param' => 'DCRGB1',
        'param' => 'designColorRgb1',
        'parse_value' => function( $val, $param, $post_id ) {
          return sfw_leadinghashit( (string) $val );
        }
      ),

      array(
        'short_param' => 'DCRGB2',
        'param' => 'designColorRgb2',
        'parse_value' => function( $val, $param, $post_id ) {
          return sfw_leadinghashit( (string) $val );
        }
      ),

      array(
        'short_param' => 'DCRGB3',
        'param' => 'designColorRgb3',
        'parse_value' => function( $val, $param, $post_id ) {
          return sfw_leadinghashit( (string) $val );
        }
      ),


      // --- Product relevant deeplinks

      array(
        'short_param' => 'P',
        'param' => 'productId',
      ),

      array(
        'short_param' => 'AP',
        'param' => 'appearanceId',
      ),

      array(
        'short_param' => 'SIZE',
        'param' => 'sizeId',
      ),

      array(
        'short_param' => 'V',
        'param' => 'viewId',
      ),

      // --- Confomat related

      array(
        'short_param' => 'TAB',
        'param' => 'panel',
      ),

      // --- custom Text

      array(
        'short_param' => 'TXT1',
        'param' => 'tx1',
      ),

      array(
        'short_param' => 'TXT2',
        'param' => 'tx2',
      ),

      array(
        'short_param' => 'TXT3',
        'param' => 'tx3',
      ),

      array(
        'short_param' => 'TXTRGB',
        'param' => 'textColorRgb',
        'parse_value' => function( $val, $param, $post_id ) {
          return sfw_leadinghashit( (string) $val );
        }
      ),

      array(
        'short_param' => 'TXTPC',
        'param' => 'textColor',
      ),

      // --- other

      array(
        'short_param' => 'T',
        'param' => 'productTypeId',
      ),

      array(
        'short_param' => 'D',
        'param' => 'departmentId',
      ),

      array(
        'short_param' => 'DP',
        'param' => 'productTypeCategoryId',
      ),

      array(
        'short_param' => 'G',
        'param' => 'designCategoryId',
      ),

      array(
        'short_param' => 'S',
        'param' => 'designSearch',
      ),

	) );

}


