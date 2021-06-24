<?php if ( ! defined( '\ABSPATH' ) ) exit;


// add shortcode
sfw_add_shortcode( 'shipping-calculator', 'sfw_shortcode_shipping_callback' );

function sfw_shortcode_shipping_callback( $atts, $content ){

  /*
  $atts = shortcode_atts( array(
		'shipping-calculator' => '',
	), $atts );*/

  return '<div data-shipping class="sfw"></div>';

}
