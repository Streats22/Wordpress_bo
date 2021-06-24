<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
* Get all Sizes
*
* @param  mixed $producttype_selector See sfw_get_producttype_id()
* @return false|object
*
* @since 1.0.0
*/

function sfw_get_sizes( $producttype_selector = false ) {

  return !empty( $producttype = sfw_get_producttype( $producttype_selector ) )
    ? $producttype->sizes
    : false;
}




/**
 * Get a Size Loop
 *
 * @uses Sfw_Node_Loop
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return object
 *
 * @since 1.0.0
 */

function sfw_get_size_loop( $producttype_selector = false ) {

  $producttype_id = sfw_get_producttype_id( $producttype_selector );


  $found = false;
  $size_loop = wp_cache_get( $producttype_id, 'size-loop', false, $found );

  if( !$found ) {

    $producttype = sfw_get_producttype( $producttype_id );

    $size_loop = !empty( $producttype )
      ? new Sfw_Node_Loop( $producttype->sizes )
      : false;

    wp_cache_set( $producttype_id, $size_loop,  'size-loop' );
  }

  return $size_loop;
}




/**
 * Iterate through Sizes. Use it like have_posts
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_have_sizes( $producttype_selector = false ) : bool {

  $producttype_id = sfw_get_producttype_id( $producttype_selector );

  if( empty( $size_loop = sfw_get_size_loop( $producttype_id ) ) )
    return false;


  $have = $size_loop->have_nodes();

  // the loop changed, so refresh cache
  wp_cache_set( $producttype_id, $size_loop,  'size-loop' );


  return $have;
}




/**
 * Check if the Size Loop is runnig
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return bool
 *
 * @since 1.0.0
 */

function sfw_sizes_in_the_loop( $producttype_selector = false ) : bool {

  return !empty( $size_loop = sfw_get_size_loop( $producttype_selector ) )
    ? $size_loop->in_the_loop()
    : false;
}




/**
 * Get the current Size
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_current_size( $producttype_selector = false ) {

  $size_loop = sfw_get_size_loop( $producttype_selector );

  return $size_loop->in_the_loop()
    ? $size_loop->current_node()
    : false;
}




/**
 * Get either the current Size or a specified size of the current ProductType
 *
 * @param  boolean $size_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_size( $size_id = false, $producttype_selector = false ) {

  if( empty( $size_id ) ) {

    return sfw_get_current_size( $producttype_selector );
  }

  return sfw_search_array_node( sfw_get_sizes( $producttype_selector ), 'id', $size_id );
}




/**
 * Get the Id of the current Size
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 * @return false|int
 *
 * @since 1.0.0
 */

function sfw_get_size_id( $producttype_selector = false ) {

  return !empty( $size = sfw_get_size( false, $producttype_selector ) )
    ? (int)$size->id
    : false;
}




/**
 * Echoes the Id of the current Size
 *
 * @since 1.0.0
 */

function sfw_size_id( $producttype_selector = false ) {

  echo sfw_get_size_id( $producttype_selector );
}




/**
 * Get the first sizeId from productType
 * Must be inside the Size Loop
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 * @return false|int
 *
 * @since 1.0.0
 */

function sfw_get_default_size_id( $producttype_selector = false ) {

  return !empty( $producttype = sfw_get_producttype( $producttype_selector ) )
    ? apply_filters('sfw/size/default', (int) $producttype->sizes[0]->id, $producttype )
    : false;
}




/**
 * Echo the first sizeId from productType
 * Must be inside the Size Loop
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_default_size_id( $producttype_selector = false ) {

  echo sfw_get_default_size_id( $producttype_selector );
}




/**
 * Get the name of the current size
 *
 * @param  boolean $size_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_size_name( $size_id = false, $producttype_selector = false ) {

  return !empty( $size = sfw_get_size( $size_id, $producttype_selector ) )
    ? apply_filters('sfw/size/name', (string) $size->name )
    : false;
}




/**
 * Echoes the name of the current size
 *
 * @param  boolean $size_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_size_name( $size_id = false, $producttype_selector = false ) {

  echo sfw_get_size_name( $size_id, $producttype_selector );
}




/**
 * get the measure node
 *
 * @param  boolean $size_id
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|object
 *
 * @since 1.0.0
 */

function sfw_get_size_measures( $size_id = false, $producttype_selector = false ) {

  return !empty( $size = sfw_get_size( $size_id, $producttype_selector ) )
    ? apply_filters('sfw/size/measures', $size->measures )
    : false;
}




/**
 * Generate a HTML Table with Sizes and Measures
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 * @return string
 *
 * @since 1.0.0
 */

function sfw_get_size_fit_table( $producttype_selector = false  ) {

    if( !empty( $producttype = sfw_get_producttype( $producttype_selector ) ) ) {

      $default_measures = sfw_get_size_measures( sfw_get_default_size_id() );

			$html= '<tr><th></th>';

			foreach( $default_measures as $measure ){

				$html.=	sprintf('<th><span class="sfw-measure sfw-measure-%s">%s</span></th>', sanitize_key( $measure->name), $measure->name );
			}

			$html.= '</tr>';


			while( sfw_have_sizes() ) {

				$html.= sprintf( '<tr><td><span class="sfw-fit-size">%s</span></td>', sfw_get_size_name() );

				foreach( sfw_get_size_measures() as $measure ){

					$measure_f = sfw_measure_format( $measure );
					$html.= sprintf( '<td>%s</td>', $measure_f[2] );
				}

				$html.= '</tr>';

			}

			return sprintf( '<div id="size-table" class="sfw-table-container"><table class="sfw-table sfw-size-fit-table" cellpadding="5">%s</table></div>', $html );
    }
	}




/**
 * Echo a HTML Table with Sizes and Measures
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_size_fit_table( $producttype_selector = false ) {

  echo sfw_get_size_fit_table( $producttype_selector );
}




/**
 * Get the current sizeFitHint.
 *
 * The default fitHint "normal" will be stripped by filter
 *
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @return false|string
 *
 * @since 1.0.0
 */

function sfw_get_size_fit_hint(  $producttype_selector = false ){

    return !empty($producttype = sfw_get_producttype( $producttype_selector ) )
      ? apply_filters( 'sfw/producttype/sizefithint', $producttype -> sizeFitHint )
      : false;
}




/**
 * Echoes the current sizeFitHint.
 *
 * @param  string  $before
 * @param  string  $after
 * @param  mixed $producttype_selector See sfw_get_producttype_id()
 *
 * @since 1.0.0
 */

function sfw_size_fit_hint( $before = '', $after = '',  $producttype_selector = false  ) {

  sfw_conditional_echo( sfw_get_size_fit_hint( $producttype_selector ), $before, $after );
}




/**
 * Removes the normal SizeFitHint as it has no benefit for the customer
 *
 * @ignore
 * @since 1.0.0
 */

add_filter('sfw/producttype/sizefithint', '_sfw_hook_remove_normal_size_fit_hint', 2 );

function _sfw_hook_remove_normal_size_fit_hint( $fithint ) {

  if( $fithint == 'normal' ) {
    return false;
  }

  return $fithint;
}




