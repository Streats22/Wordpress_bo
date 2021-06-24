<?php if ( ! defined( '\ABSPATH' ) ) exit;




// Create Shop options admin page


sfw_register_admin_page( array(

	'page_title' 		=> __('Shop Settings', 'apparelcuts-spreadshirt' ),

	'menu_title'		=> _x( 'Settings', 'menu name', 'apparelcuts-spreadshirt' ),

	'menu_slug' 		=> 'sfw-shop-settings',

	'autoload' 			=> 'yes'

) );




// for easier fieldkey use
sfw_remember_field_key( 'field_5bc08342585fe', 'sfw_shop_host' );
sfw_remember_field_key( 'field_5bc08280585fd', 'sfw_shop_id' );
sfw_remember_field_key( 'field_5bc0854f58600', 'sfw_shop_apikey' );
sfw_remember_field_key( 'field_5bc0857a58601', 'sfw_shop_secret' );


sfw_remember_field_key( 'field_5c94f2e2d5dd1', 'sfw-sync-product-post-status' );




/**
 * Validate the Shop ID
 */

add_action( 'acf/validate_value/key='.sfw_field_key( 'sfw_shop_id' ), function( $valid, $value, $field, $input ){

  if( !$valid )
    return $valid;

  if( empty( $_POST['acf'][ sfw_field_key('sfw_shop_apikey') ]) )
    return __( 'We need your Apikey to verify the Shop Id' );


  $api = new WP_Spreadshirt_Api( array(
    'host'   => $_POST['acf'][ sfw_field_key('sfw_shop_host') ],
    'apikey' => $_POST['acf'][ sfw_field_key('sfw_shop_apikey') ],
    'secret' => $_POST['acf'][ sfw_field_key('sfw_shop_secret') ]
  ));


  $url = sfw_create_request_url( 'shops', $value );
  $shop = $api->get( $url );

  if( is_wp_error( $shop ) ) {

		if( $shop->get_error_message() === 'Not found' )
			return __( 'We are unable to find this shop. Did you choose the correct host?', 'apparelcuts-spreadshirt' );

    return $shop->get_error_message();
  }


	// verify apikey and secret
	/*
	currently the API accepts any apikey and secret combination, so we can't verify them

	$payload = json_encode(
		array(
			'basket' => array(
				'shop' => array(
					'id' => $value
				)
			)
		)
	);

	$basket = $api->post( $shop->baskets->href, array(), array(
		'send_secret' => true,
		'body' => $payload
	) );

  echo '<pre style="color:tomato;">';
  var_dump( $basket );
  echo '</pre>';
  die();

	*/

  return $valid;

}, 10, 4  );




/**
* trigger shop configuration updated hook
*/

add_action( 'acf/save_post', function( $post_id ){

	// check if we save the correct page
	if( $post_id !== 'options' )
		return;


	if( isset( $_POST['acf'][ sfw_field_key( 'sfw_shop_id' ) ] ) || isset( $_POST['acf'][ sfw_field_key( 'sfw_shop_apikey' ) ] ) ) {

		flush_rewrite_rules();

		do_action( 'sfw/shopoptions/updated' );
	}


}, 20 );




/*
* Trigger Hook before Options are displayed
*/
add_action( 'sfw/admin-page/load-sfw-shop-settings', function(){
	do_action( 'sfw/prepare-shop-settings' );
});






/**
* Add a Admin Notice if shop was not configured already
*/

add_action( 'admin_notices', function () {

	if( sfw_is_shop_properly_configured() )
		return;

  ?><div class="update-nag dismissable">
      <span><?php _e( 'Thanks for installing <em>Spreadshirt for Wordpress by Apparelcuts</em>! Please <a href="'.admin_url('admin.php?page=sfw-shop-settings').'">visit the settings page</a> and enter your shop id!', 'apparelcuts-spreadshirt' ); ?></span>
  </div><?php

});




/*
*  prevent changing the shop id or host after initation
*/

add_action( 'acf/prepare_field', function( $field ){

	if( $field['key'] === sfw_field_key( 'sfw_shop_host' ) || $field['key'] === sfw_field_key( 'sfw_shop_id' ) ) {

		if( sfw_is_shop_properly_configured() && !apply_filters( 'sfw/allow_shop_change', true === sfw_constant('ALLOW_SHOP_CHANGE')  ) ) {

			$field['disabled'] = true;
		}
	}

	return $field;

});




/*
 * Add a "change options" warning to shop id and platform
 */

/**
 * @ignore
 * @since  1.0.0
 */

function _sfw_callback_shop_change_warning() {
	if( sfw_is_shop_properly_configured() && !apply_filters( 'sfw/allow_shop_change', true === sfw_constant('ALLOW_SHOP_CHANGE')  ) ) {

		$text = sprintf( __('It is not recommended to change this setting after the shop was initiated. To change it anyways, you have to add %s to your wp-config file.', '%s = code block', 'apparelcuts-spreadshirt' ), '<code>define("SFW_ALLOW_SHOP_CHANGE", true );</code>');

		printf(
			'<p class="acf-shop-change-warning sfw-acf-extended-instructions">%s</p>',
			$text
		);
	}
}

sfw_acf_append_to_field( sfw_field_key( 'sfw_shop_id' ), '_sfw_callback_shop_change_warning' );
sfw_acf_append_to_field( sfw_field_key( 'sfw_shop_host' ), '_sfw_callback_shop_change_warning' );



/*
 * Unautoload some options ( ACF autoloads all on that options page by defaults )
 */

add_action( 'sfw/shopoptions/updated', function(){

	$options = array(
		'sfw-sync-product-post-status',
		'sfw-sync-remote-timeout',
		'sfw-sync-remote-timeout'
	);

	sfw_acf_unautoload_options( $options );

},100 );
