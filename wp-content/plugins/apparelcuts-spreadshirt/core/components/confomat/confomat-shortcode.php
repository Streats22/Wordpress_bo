<?php if ( ! defined( '\ABSPATH' ) ) exit;




// add shortcode
sfw_add_shortcode( 'confomat', 'sfw_shortcode_confomat_callback' );
sfw_add_shortcode( 'designer', 'sfw_shortcode_confomat_callback' );




/**
 * Confomat Shortcode Callback
 *
 * @ignore
 * @since  1.0.0
 */

function sfw_shortcode_confomat_callback( $atts, $content ){

  /*$atts = shortcode_atts( array(
		'confomat' => '',
	), $atts ); */

  $atts = wp_parse_args( $atts, array(
    'confomat' => ''
  ));


  $params = array();

  // get params from post
  if( is_numeric( $atts['confomat'] ) ) {

    $post = get_post( $atts['confomat'] );

    if( sfw_is_wp_post( $post ) || 'publish' == $post->post_status || is_preview() ) {

      $params = sfw_get_confomat_params_from_post_meta( $post->ID );
    }
  }
  else {
    // parse params from attributes
    $possible_params = sfw_get_possible_confomat_params();
    foreach( $atts as $att => $val ) {
      foreach( $possible_params as $possible_param ) {
        if( 0 === strcasecmp( $att, $possible_param['param'] ) ) {
          if( is_scalar( $val ) )
            $params[ $possible_param['param'] ] = (string) $val;
          break;
        }
      }
    }
  }

  return sfw_get_confomat_embed_code( $params );

}
