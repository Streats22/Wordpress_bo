<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Includes the Advanced Custom Fields Plugin if it is not
 * already installed as individual plugin
 *
 * @todo change paths for pro version
 * @ignore
 */



// fallback to internal ACF
if( !class_exists('acf')) {

	// 1. customize ACF path
	add_filter('acf/settings/path', 'sfw_acf_settings_path');

	function sfw_acf_settings_path( $path ) {
    //$path = sfw_pro_or( sfw_dir( 'pro/resources/advanced-custom-fields-pro/' ), sfw_dir( 'resources/advanced-custom-fields/' ) );
	  $path = sfw_dir( 'resources/advanced-custom-fields/' );
    return $path;
	}

	// 2. customize ACF dir
	add_filter('acf/settings/dir', 'sfw_acf_settings_dir');

	function sfw_acf_settings_dir( $dir ) {
    //$dir =  sfw_pro_or( sfw_url( 'pro/resources/advanced-custom-fields-pro/' ), sfw_url( 'resources/advanced-custom-fields/' ) );
	  $dir =  sfw_url( 'resources/advanced-custom-fields/' );
    return $dir;
	}

	// 3. Hide ACF field group menu item
	if( !defined( 'SFW_ACF_ALWAYS' ) )
		add_filter('acf/settings/show_admin', '__return_false');


	// 4. Include ACF
	//sfw_include( sfw_pro_or( 'pro/resources/advanced-custom-fields-pro/acf.php' , 'resources/advanced-custom-fields/acf.php' ) );
	sfw_include( 'resources/advanced-custom-fields/acf.php' );

}
else {

	// if ACF Free is enabled, show a warning

	/*
	if( sfw_is_pro() && !sfw_can_use_acf_pro() ) {

		add_action( 'admin_notices', 	function() {
		    ?>
		    <div class="notice notice-error">
		        <p><?php _e( 'Hey! Thank you for using Spreadshirt for Wordpress Pro! Unfortunatly, we noticed that you also use the free Version of Advanced Custom Fields, which is currently not fully compatible.', 'sample-text-domain' ); ?>
		        <?php _e( 'If you continue to use it, you may encounter some interface issues.', 'sample-text-domain' ); ?>
	        	<p><?php printf(_x( 'You can disable your Plugin, so we can include the pro version for you. Or head over to %s and support this amazing plugin by buying a license.', '%s link to acf', 'apparelcuts-spreadshirt' ), '<a href="https://www.advancedcustomfields.com" target="_blank">advancedcustomfields.com</a>' ); ?>
		    </div>
		    <?php
		});

	}
	*/


}
