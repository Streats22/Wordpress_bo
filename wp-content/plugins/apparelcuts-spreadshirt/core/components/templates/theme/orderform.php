<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
* print opening orderform for article
*
* @since 1.0.0
*/

function sfw_orderform_open( $article_id = false ) {

  $article_id = sfw_get_article_id( $article_id );

  if( !$article_id )
    return;

  $GLOBALS['sfw-in-orderform'] = $article_id;

  $html = sprintf(
    '<form class="sfw sfw-form sfw-orderform" data-article="%1$s">',
    esc_attr( $article_id )
  );

  echo apply_filters( 'sfw/orderform/opening_tag', $html );

  // @todo codex
  do_action( 'sfw/orderform/before', $article_id );

}




/**
* print closing orderform tags
*
* @since 1.0.0
*/

function sfw_orderform_close( ) {


  sfw_orderform_hidden_fields();

  // @todo codex
  do_action( 'sfw/orderform/after' );

  $GLOBALS['sfw-in-orderform'] = false;

  echo '</form>';

}




/**
* Prints add to basket button
*
* @since 1.0.0
*/

function sfw_add_to_cart_button() {

  printf(
    '<span data-add-to-basket disabled class="sfw-button --large">%s</span>',
    __( 'Add to Cart', 'apparelcuts-spreadshirt' )
  );

}




/**
* echo some required hidden fields for basket Operations
*
* @todo add is editable logic
* @since 1.0.0
*/

function sfw_orderform_hidden_fields(){

	// the following two are for future compatibility
  ?>
	<input type="hidden" name="disallow-appearance-change"  value="0"/>
	<input type="hidden" name="disallow-edit"  		          value="0"/>
	<input type="hidden" name="edit" 				                value="<?php sfw_article_customize_link() ?>"/>

	<input type="hidden" name="producttype-id" 		          value="<?php sfw_producttype_id() ?>"/>
	<input type="hidden" name="product-id" 			            value="<?php sfw_product_id() ?>"/>
	<input type="hidden" name="article-id" 			            value="<?php sfw_article_id() ?>"/>
	<input type="hidden" name="quantity" 			              value="<?php sfw_orderform_defaut_quantity()  ?>"/>
	<input type="hidden" name="continueShopping" 	          value="<?php echo sfw_get_continue_shopping_url() ?>"/>

  <?php /* this one is for future tracking purposes */ ?>
	<input type="hidden" name="origin" 	                  value="orderform"/>
	<?php

  // @todo codex
  do_action( 'sfw/orderform/hidden_fields' );
}




/**
* Retrieve Appearance Field
*
* @since 1.0.1
*/

function sfw_get_orderform_appearance_field() {

  $code = sprintf( '<input type="hidden" name="appearance" value="%s"/>', sfw_get_article_appearance_id() );

  return apply_filters( 'sfw/orderform/appearance', $code, sfw_get_article_appearance_id() );
}




/**
 * Print Appearance Field
 *
 * @return [type]
 * @since  1.0.1
 */

function sfw_orderform_appearance_field() {

  echo sfw_get_orderform_appearance_field();
}




/**
* Retrieve Size Field
*
* @since 1.0.1
*/

function sfw_get_orderform_size_field() {

  $code = '';

  while( sfw_have_sizes() ):

    $code = sprintf( '<input type="hidden" name="size" value="%s"/>', esc_attr( sfw_get_size_id() ) );

  endwhile;

  return apply_filters( 'sfw/orderform/size', $code, sfw_get_article_appearance_id() );
}




/**
 * Print Size Field
 *
 * @return [type]
 * @since  1.0.1
 */

function sfw_orderform_size_field() {

  echo sfw_get_orderform_size_field();
}





/**
* Retrieve the default quantity
*
* @return int
* @since 1.0.0
*/

function sfw_get_orderform_defaut_quantity() {

  return apply_filters('sfw/orderform/quantity', 1 );
}




/**
 * Echoes the default quantity
 *
 * @since  1.0.0
 */

function sfw_orderform_defaut_quantity() {

  echo sfw_get_orderform_defaut_quantity();
}




/**
* Retrieve the Continue Shopping Link
*
* @return string
* @since 1.0.0
*/

function sfw_get_continue_shopping_url() {

  return apply_filters( 'sfw/continue_shopping_url', site_url() );
}




/**
 * Retrieve a product stockstate message tag
 *
 * @param  string $text
 * @return string
 * @since  1.0.0
 */

function sfw_get_stockstate_message( $text = false ) {

  $text = $text ?: __( 'Sorry, this product is currently unavailable :(', 'apparelcuts-spreadshirt' );
  $text = apply_filters( 'sfw/sfw_product_unavailable_message', $text );

  return sprintf( '<span class="--stockstate">%s</span>', $text );
}


/**
 * Echo a product stockstate message tag
 *
 * @param  string $text
 * @return string
 * @since  1.0.0
 */

function sfw_stockstate_message( $text = false ) {

  echo sfw_get_stockstate_message( $text );
}




/**
 * Prints product uavailable message
 *
 * @param  string $text
 * @since  1.0.0
 */

function sfw_product_unavailable_message( $text = false ) {

  echo sfw_get_product_unavailable_message( $text );
}

//add_action( 'sfw/theme/orderform/before_price', 'sfw_product_unavailable_message' );




/**
 * Retrieve product customize button
 *
 * @param  string $text
 * @return string
 * @since  1.0.0
 */

function sfw_get_product_customize_button( $text = false ) {

  $text = $text ?: __( 'Customize', 'apparelcuts-spreadshirt' );
  $text = apply_filters( 'sfw/sfw_product_customize_button', '<i class="ac ac-shirt-edit"></i>' . $text );

  return sprintf( '<a href="%s" class="sfw-button sfw-button-customize --large">%s</a>', sfw_get_article_customize_link(), $text );
}




/**
 * Echoes product customize button
 *
 * @param  string $text
 * @since  1.0.0
 */

function sfw_product_customize_button( $text = false ) {

  echo sfw_get_product_customize_button( $text );
}

add_action( 'sfw/theme/orderform/actions', 'sfw_product_customize_button' );

