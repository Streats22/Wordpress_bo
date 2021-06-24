<?php if ( ! defined( '\ABSPATH' ) ) exit;

sfw_include( 'core/components/shop/functions-shop.php' );

sfw_include( 'core/components/shop/functions-shop-options.php' );


// save and load  "Shop Settings"
sfw_add_acf_load_point( sfw_path( 'assets/acf' ) );

sfw_add_acf_group_save_point( 'group_5bc0827a93c55' );

sfw_remember_field_key( 'group_5bc0827a93c55', 'group-api-settings' );

sfw_add_acf_group_save_point( 'group_5c934ea3a8752' );

sfw_remember_field_key( 'group_5bc0827a93c55', 'group-general-settings' );


add_action( 'sfw/shopoptions/updated', 'sfw_validate_shop_configuration', 10 );

add_action( 'sfw/shopoptions/updated', 'sfw_update_noapi_shop_metadata',  20 );

sfw_include( 'core/components/shop/shop-options.php' );