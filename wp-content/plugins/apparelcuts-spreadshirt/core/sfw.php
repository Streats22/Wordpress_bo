<?php if ( ! defined( '\ABSPATH' ) ) exit;



if( !class_exists( 'SFW' ) ) {


  /**
   * The main Plugin core
   *
   * Loads the plugin files, checks if the shop is configured
   *
   * @since 1.0.0
   */

  class SFW {


    /**
     * @ignore
     */

    function __construct() {

      // silence
    }




    /**
     * Load the core
     *
     * @since 1.0.0
     */

    function load() {

      // Includes

      sfw_include( 'core/includes/functions-format.php');

      sfw_include( 'core/includes/functions-wp-helpers.php');

      sfw_include( 'core/includes/functions-wp-errors.php');

    	sfw_include( 'core/includes/class-entity-controller.php');

      sfw_include( 'core/includes/class-entity.php');

    	sfw_include( 'core/includes/functions-entity.php');

    	sfw_include( 'core/includes/functions-entity-getter.php');

    	sfw_include( 'core/includes/functions-load.php');

      sfw_include( 'core/includes/class-wp-spreadshirt-api.php');

      sfw_include( 'core/includes/class-wp-rest-spreadshirt-api.php');

    	sfw_include( 'core/includes/functions-api.php');

      sfw_proxy_add_api_args();

    	sfw_include( 'core/includes/class-remote-cache.php');

    	sfw_include( 'core/includes/class-id-cache.php');

    	sfw_include( 'core/includes/class-node-loop.php');

      sfw_include( 'core/includes/class-spreadshirt-image.php');

    	sfw_include( 'core/includes/functions-admin.php');

    	sfw_include( 'core/includes/functions-wp-rest.php');

    	sfw_include( 'core/includes/functions-taxonomy-screens.php');

    	sfw_include( 'core/includes/acf/acf-utils.php');

    	sfw_include( 'core/includes/acf/location-sfw-active.php');

    	sfw_include( 'core/includes/functions-previews.php');


      // Components
    	sfw_include( 'core/components/assets.php');

    	sfw_include( 'core/components/admin-pages/admin-pages.php');

    	sfw_include( 'core/components/admin/admin.php');

      sfw_include( 'core/components/functions-country.php');

      sfw_include( 'core/components/functions-currency.php');

      sfw_include( 'core/components/functions-language.php');

      sfw_include( 'core/components/functions-locale.php');

      sfw_include( 'core/components/functions-hyperlinks.php');

      sfw_include( 'core/components/functions-image.php');

      sfw_include( 'core/components/functions-price.php');

      sfw_include( 'core/components/shortcode.php');

      sfw_include( 'core/components/shipping-costs.php');

    	sfw_include( 'core/components/shop/shop.php');

      sfw_include( 'core/components/synchronization/synchronization.php');

    	sfw_include( 'core/components/product-article/product-article.php');

    	sfw_include( 'core/components/design/design.php');

    	sfw_include( 'core/components/printtype/printtype.php');

    	sfw_include( 'core/components/producttype/producttype.php');

    	sfw_include( 'core/components/brand/brand.php');

      sfw_include( 'core/components/dynamic-pages/dynamic-pages.php');

      sfw_include( 'core/components/basket/basket.php');

      sfw_include( 'core/components/external-pages.php');

      sfw_include( 'core/components/confomat/confomat.php');

      sfw_include( 'core/components/templates/templates.php');

      sfw_include( 'core/components/load.php');

      sfw_include( 'core/deprecated.php');


      do_action( 'sfw/include/core' );


      // trigger init after other plugins have been loaded

      add_action( 'plugins_loaded', array( $this, 'init_shop' ), 5 );


    }






    /**
     *
     * Triggers the init action of the Shop Components
     *
     * Triggers only if the shop was previously configured
     *
     * @since 1.0.0
     */

    function init_shop() {

      // validate that the shop configuration is set
			if( !sfw_is_shop_properly_configured() ) {

        /**
         * This runs before the shop was configured
         *
         * @var WP_Error $error An Error indicating the reason
         */

				do_action( 'sfw/installing', new WP_Error('no-configuration', __( 'No working shop configuration found' ) ) );

      }
      elseif( sfw_is_shop_properly_configured() && !sfw_get_shop() ) {

        /**
         * This runs when the plugin is configured but could not be initiated
         *
         * @var WP_Error $error An Error indicating the reason
         */

				do_action( 'sfw/init/failed', new WP_Error('no-configuration', __( 'Could not load shop data' ) ) );

			}

      else {


        do_action( 'sfw/before_init' );

        /**
         * Triggers after the shop was successfully loaded and the shop configuration
         * is working correctly
         *
         * This is the best hook for plugin authors to init their extensions
         *
         * @since 1.0.0
         */

        do_action( 'sfw/init' );

      }


      /**
       * Fires always after the plugin has been loaded.
       *
       * @see sfw/init
       */
      do_action( 'sfw/init/always' );

    }



  } // end class


  /**
   * Retrieve the main plugin instance
   *
   * Triggers the load method if no instance was instantiated previously
   *
   * @return object Instance of SFW
   *
   * @since  1.0.0
   */

  function sfw() {

    global $sfw_core;

    if( !$sfw_core instanceof SFW ) {
      $sfw_core = new SFW();
      $sfw_core->load();
    }

    return $sfw_core;
  }

  // load plugin
  sfw();


} // end class_exists



