<?php if ( ! defined( '\ABSPATH' ) ) exit;





/**
 * Return Array of settings that will be used to create a confomat instance
 *
 * @param  array  $args
 * @return array
 * @since  1.0.0
 */

function sfw_get_confomat_settings( $args = array() ) {

  $defaults = apply_filters('sfw/confomat/defaults', array(
		'shopUrl' 							=> sfw_get_customize_link().'#!',
		'shareUrlTemplate' 			=> sfw_get_customize_link( array('P' => '{PRODUCT_ID}', 'V' => '{VIEW_ID}') ),
    'paramlist'             => wp_list_pluck( sfw_get_possible_confomat_params(), 'param', 'short_param' )
  ));

  $args = wp_parse_args( $args, $defaults );

  return apply_filters('sfw/confomat/args', $args );
}




/**
 * Retrieve a Confomat Embed Code
 *
 * @param  array  $args
 * @return string
 * @since  1.0.0
 */


function sfw_get_confomat_embed_code( $args = array() ) {

  if( sfw_constant( 'LOAD_CONFOMAT' ) ) {
    sfw_doing_it_wrong( __FUNCTION__, "Confomat should be loaded only once per page load." );
    return;
  }

  // remember call
  sfw_define( 'LOAD_CONFOMAT', true );

  // enqueue confomat script
  sfw_wp_enqueue_footer_script( 'sfw-confomat' );

  // get args
  $settings = sfw_get_confomat_settings( $args );

  return apply_filters( 'sfw/confomat',
    sprintf( '<div data-confomat="%s"></div>', htmlspecialchars( json_encode( $settings ), ENT_QUOTES, 'UTF-8') ),
    $settings
  );
}




/**
 * Displays the T-Shirt Designer
 *
 * @see sfw_get_confomat_embed_code
 * @param  array $args
 * @return string
 * @since  1.0.0
 */

function sfw_confomat( $args = array() ) {

  return sfw_get_confomat_embed_code( $args );
}









function _sfW_hook_confomat_the_content( $content ){

	global $post;

	if( $post->post_type != 'sfw-confomat' )
		return $content;

	$content = sfw_confomat( sfw_get_confomat_params_from_post_meta( get_the_ID() ) );

	return $content;
}

add_filter( 'the_content', '_sfW_hook_confomat_the_content', 100 );




/**
 * Retrieve post meta and generates confomat configuration
 *
 * @param  int $post_id
 * @return array
 * @since  1.0.0
 */

function sfw_get_confomat_params_from_post_meta( $post_id ) {

  $possible_params = sfw_get_possible_confomat_params();

  $parsed_params = array();

  foreach( $possible_params as $param ) {

    $val = get_field( $param['param'], $post_id );

    if( !empty( $val ) && isset( $param['parse_value'] ) && is_callable( $param['parse_value'] ) ) {

      $val = call_user_func( $param['parse_value'], $val, $param, $post_id );

    }

    if( $val != '' && !is_null( $val ) ) {

      $parsed_params[ $param['param'] ] = $val;
    }

  }


  // additional fields
  $parsed_params['parse_from_url'] = (bool) get_field('sfw-confomat-parse-from-url', $post_id );
  $parsed_params['sync_hash']      = (bool) get_field('sfw-confomat-sync-product-id', $post_id );


  return apply_filters( 'sfw/get_confomat_params_from_post_meta', $parsed_params, $post_id );

}
