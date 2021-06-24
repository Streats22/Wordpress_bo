<?php if ( ! defined( '\ABSPATH' ) ) exit;




function sfw_get_add_to_basket_error_message( $text = false ) {

  $text = $text ?: __( 'Sorry, we had trouble adding your product to the cart! Please try again :(', 'apparelcuts-spreadshirt' );
  $text = apply_filters( 'sfw/sfw_add_to_basket_error_message', $text );

  return sprintf( '<p class="--msg-basket-error" style="display:none;">%s</p>', $text );
}

function sfw_add_to_basket_error_message( $text = false ) {

  echo sfw_get_add_to_basket_error_message( $text );
}

add_action( 'sfw/orderform/after', 'sfw_add_to_basket_error_message' );





function sfw_get_checkout_button( $text = false ) {

  $text = $text ?: __( 'Checkout', 'apparelcuts-spreadshirt' );
  $text = apply_filters( 'sfw/checkout_button', $text );

  return sprintf( '<a data-checkout class="sfw-button sfw-button-checkout --large">%s</a>', $text );
}

function sfw_checkout_button( $text = false ) {

  echo sfw_get_checkout_button( $text );
}



