<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
* Registers all assets, but does not enqueue any
*
* @since 1.0.0
*/

function sfw_enqueue_scripts() {


  ////// Icons Library

  wp_register_style(
    'apparelcuts-icons',
    sfw_url( sfw_pro_or( 'pro/' ) . 'resources/apparelcuts-icons/apparelcuts-icons.css' ),
    array(),
    sfw_version()
  );


  ////// Admin

  wp_register_style(
    'sfw-admin',
    sfw_url( 'assets/css/sfw-admin.css' ),
    array(),
    sfw_version()
  );

	wp_register_script(
		'sfw-admin',
    sfw_url( 'assets/dist/sfw-admin.js' ),
    array( 'wp-hooks' ),
    sfw_version(),
		true // in footer
	);


  ////// Synchronization

  wp_register_style(
    'sfw-synchronization',
    sfw_url( 'assets/css/sfw-synchronization.css' ),
    array(),
    sfw_version()
  );


  $sync_dependencies = array( 'wp-hooks' );

  if( sfw_is_pro() ) {

    wp_register_script(
      'sfw-synchronization-pro',
      sfw_url( 'pro/assets/dist/sfw-synchronization-pro.js' ),
      array( 'wp-hooks' ),
      sfw_version(),
      true // in footer
    );

    $sync_dependencies[] = 'sfw-synchronization-pro';
  }


	wp_register_script(
    'sfw-synchronization',
    sfw_url( 'assets/dist/sfw-synchronization.js' ),
    $sync_dependencies,
    sfw_version(),
		true // in footer
	);


  // translations
  wp_set_script_translations( 'sfw-synchronization', 'apparelcuts-spreadshirt', sfw_path( 'languages/js/json' ) );


  ////// Public

  wp_register_style(
    'sfw-public',
    sfw_url( sfw_asset_min( 'assets/css/sfw-public.min.css', 'assets/css/sfw-public.css' ) ),
    array(),
    sfw_version()
  );

	wp_register_script(
    'sfw-public',
    sfw_url( sfw_asset_min( 'assets/dist/sfw-public.min.js', 'assets/dist/sfw-public.js' ) ),
    array( 'wp-hooks' ),
    sfw_version(),
		true // in footer
	);

  // translations
  wp_set_script_translations( 'sfw-public', 'apparelcuts-spreadshirt', sfw_path( 'languages/js/json' ) );


  ////// Theme

  wp_register_style(
    'sfw-theme',
    sfw_url( sfw_asset_min( 'assets/css/sfw-theme.min.css', 'assets/css/sfw-theme.css' ) ),
    array(),
    sfw_version()
  );

	wp_register_script(
    'sfw-theme',
    sfw_url( sfw_asset_min( 'assets/dist/sfw-theme.min.js', 'assets/dist/sfw-theme.js' ) ),
    array( 'sfw-public', 'wp-hooks' ),
    sfw_version(),
		true // in footer
	);

  // translations
  wp_set_script_translations( 'sfw-theme', 'apparelcuts-spreadshirt', sfw_path( 'languages/js/json' ) );


}

add_action( 'wp_enqueue_scripts',    'sfw_enqueue_scripts', 1 );
add_action( 'admin_enqueue_scripts', 'sfw_enqueue_scripts', 1 );




/**
* Enqueues Script & CSS for the Admin
*
* @since 1.0.0
*/

function sfw_admin_enqueue_scripts() {

  wp_enqueue_style( 'sfw-admin' );

  //wp_enqueue_scripts( 'sfw-admin' );

  wp_enqueue_style( 'apparelcuts-icons' );

}

add_action( 'admin_enqueue_scripts', 'sfw_admin_enqueue_scripts', 15 );




/**
* Enqueues Script & CSS for the Visitor
*
* @since 1.0.0
*/

function sfw_public_enqueue_scripts() {

  if( did_action( 'sfw/init' ) ) {

    wp_enqueue_style( 'apparelcuts-icons' );

    // defaults
    wp_enqueue_style( 'sfw-public' );
    wp_enqueue_script( 'sfw-public' );

    // theme
    wp_enqueue_style( 'sfw-theme' );
    wp_enqueue_script( 'sfw-theme' );

  }

}

add_action( 'wp_enqueue_scripts', 'sfw_public_enqueue_scripts', 15  );




/**
* Enqueues Core Javascript
*
* Uses a priority of 100, so that plugins will load first
*
* @since 1.0.0
*/

function sfw_core_enqueue() {


  $core_dependencies = apply_filters( 'sfw/js/core_dependencies', array( 'wp-hooks', 'jquery', 'wp-i18n' ) );


	wp_register_script(
		'sfw-core',
    sfw_url( sfw_asset_min( 'assets/dist/sfw-core.min.js', 'assets/dist/sfw-core.js' ) ),
    $core_dependencies,
    sfw_version(),
		true // in footer
	);


  // translations
  // - currently not translations in this file
  // wp_set_script_translations( 'sfw-core', 'sfw', sfw_path( 'languages/js/json' ) );


  // enqueue
  wp_enqueue_script( 'sfw-core' );


  // add data
  sfw_localize_script( 'sfw-core' );

}

add_action( 'admin_enqueue_scripts', 'sfw_core_enqueue', 100 );
add_action( 'wp_enqueue_scripts', 'sfw_core_enqueue', 100  );





/**
* Prepare some basic configurations for use with javascript
*
* @param string $scripthandle - name of the script to add settings to
* @param array $configuration - a default set of configurations
*
* @since 1.0.0
*/

function sfw_localize_script( $scripthandle ) {

  $args = array(
    'init'       => (bool) did_action( 'sfw/init' ),
    'version'    => sfw_version(),
    'debug'      => (bool) SCRIPT_DEBUG,
    'synced'     => (bool) sfw_is_synced(),
    'home'       => home_url(),
    'url'        => sfw_url(),
    'continueShoppingLink' => sfw_get_continue_shopping_url(),
    'rest'       => array(
      'url'        => sfw_rest_url(),
      'nonce'      => wp_create_nonce( 'wp_rest' ),
      'proxy_url'  => sfw_get_proxy_url(),
    ),
  );

  if( did_action( 'sfw/init' ) && is_admin() ) {

    $args['rest']['apikey'] = sfw_apikey();

    //if( current_user_can( 'manage_options' ) )
    //  $args['rest']['secret'] = sfw_secret();
  }


  if( did_action( 'sfw/init' ) ) {

    $args += array(
      'shop' => sfw_get_shop(),
      'currency' => sfw_get_currency(),
      'language' => sfw_get_language(),
      'country'  => sfw_get_country(),
      'host' => sfw_get_host(),
      'locale' => sfw_get_locale(),
    );

    /**
     * Data passed to the Javascript interface
     *
     * Only when the Shop is already configured and the 'sfw/init' Action did run
     *
     * @param array $args
     *
     * @since 1.0.0
     */

    $args = apply_filters( 'sfw/js', $args );
  }


  /**
   * Data passed to the Javascript interface
   *
   * This Hook is triggerd always, even if the Shop was not configured yet.
   *
   * @param array $args
   *
   * @since 1.0.0
   */

  $args = apply_filters( 'sfw/wp_localize_script', $args );


  $string = 'var sfw_config = ' . json_encode( $args ) . ';';

  // add data to skript
	wp_add_inline_script( $scripthandle, $string, 'before'	);

}




/**
 * Switches Script inclusion depending on SCRIPT_DEBUG constant
 *
 * @param string $compressed   Url of the minified file
 * @param string $uncompressed Url of the expanded source file
 *
 * @since 1.0.0
 */

function sfw_asset_min( $compressed, $uncompressed ) {

  if( ( defined( 'SCRIPT_DEBUG' ) && constant( 'SCRIPT_DEBUG' ) ) || sfw_do_debug() ) {
    return $uncompressed;
  }
  else {
    return $compressed;
  }
}




/**
 * enqueue script after wp_enqueue_script was probably called already
 *
 * @param  string  $handle
 * @param  integer $priority
 * @since  1.0.0
 */

function sfw_wp_enqueue_footer_script( $handle, $priority = 100  ) {

    if( did_action( 'wp_enqueue_scripts' ) ) {

      wp_enqueue_script( 'sfw-confomat');
    }
    else {

      add_action( 'wp_enqueue_scripts', function() use ( $handle ){
        wp_enqueue_script( 'sfw-confomat');
      }, $priority );
    }
}