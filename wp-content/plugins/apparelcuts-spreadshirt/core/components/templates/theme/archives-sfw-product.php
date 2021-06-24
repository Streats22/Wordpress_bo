<?php if ( ! defined( '\ABSPATH' ) ) exit;


add_action( 'sfw/theme/product-loop/after', function(){

  if( get_field( 'sfw-show-price-hint-in-archives', 'option' ) ) {

    echo '<p>';

    sfw_price_hint();

    echo '</p>';
  }
});