<?php if ( ! defined( '\ABSPATH' ) ) exit;


/*
 *  Register dynamic page
 */

sfw_register_dynamic_page( array(
	'slug' 				 => 'basket',
	'label' 			 => __( 'Cart', 'apparelcuts-spreadshirt' ),
  'instructions' => __( 'A page that displays the current basket', 'apparelcuts-spreadshirt' ),
	'post_name' 	 => sanitize_key( _x('cart', 'The basket/cart page slug', 'apparelcuts-spreadshirt' ) ),
  'post_content' => '[sfw basket]',
  '_post_type' 	 => 'page',
  'acf_append'   => function(){
    $text = sprintf(
      _x('In most cases, this page should contain the %s shorttag', '%s = shorttag code', 'apparelcuts-spreadshirt' ),
      sprintf( '<code>%s</code>', '[sfw basket]' )
    );

    printf( '<p class="sfw-acf-extended-instructions">%s</p>', $text );
  }
) );




/*
 *  add secret to basket api requests
 */

add_filter( 'wp-rest-spreadshirt/request_args', function( $args, $url, $endpoint ) {

  if( !sfw_is_shop_properly_configured() )
    return $args;

  if( 0 !== strpos( $endpoint, 'baskets' ) )
    return $args;

  $args['send_secret'] = true;

  return $args;

}, 10, 3 );




/*
 *  Shortcode
 */

/**
 * Do basket shortcode
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_callback_shortcode_basket( $atts, $content ){

  $atts = shortcode_atts( array(
		'basket' => '',
	), $atts );


  return '<div data-basket class="sfw"></div><div>' . sfw_get_checkout_button() . '</div>' ;
}

sfw_add_shortcode( 'basket', '_sfw_callback_shortcode_basket' );
sfw_add_shortcode( 'cart',   '_sfw_callback_shortcode_basket' );


