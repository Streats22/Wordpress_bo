<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
Plugin Name: Apparelcuts Spreadshirt for Wordpress
Plugin URI: https://www.apparelcuts.com/resources/spreadshirt-for-wordpress-plugin/
Description: Syncs your Spreadshop with your Wordpress Blog.
Version:1.0.16
Author: Apparelcuts.com, Oh!Fuchs
Author URI: https://ohfuchs.com
Text Domain: apparelcuts-spreadshirt
Domain Path: /languages
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/
// test

if( !class_exists( 'SFW_Plugin' ) ) {



  /**
   * Handles loading the Plugin Core
   * @ignore
   */

  class SFW_Plugin {




    // if this is the pro plugin
    public $is_pro = false;




    public $storage = array();





    function init() {

      $this->file = __FILE__;
      $this->dir  = plugin_dir_url(  __FILE__ );
      $this->path = plugin_dir_path( __FILE__ );
      $this->languages = trailingslashit( basename( dirname( __FILE__ ) ) );

      $this->version     = require( $this->path . 'version.php' );
      // supported php min, max
      $this->version_php = array( '7.0', false );
      // supported wordpress min, max
      $this->version_wp  = array( '5.1', false );

      register_activation_hook( __FILE__, array( $this, 'on_activation' ) );
      // register_deactivation_hook( __FILE__, array( $this, 'on_deactivation' ) );

      $pro_file = $this->path . 'pro/sfw-pro.php';
      $this->is_pro = file_exists( $pro_file );

      require_once( $this->path . 'core/utils.php'  );
      require_once( $this->path . 'core/l10n.php'  );
      require_once( $this->path . 'core/debug.php'  );

      if( $this->is_pro ) {
        require_once( $pro_file  );
      }

      require_once( $this->path . 'core/sfw.php'  );



    }

    function is_compatible() {

      if( version_compare( phpversion(), $this->version_php[0], '<' ) )
        return new WP_Error( 'not-compatible', 'The plugin does not support your version of php.');

      else if( $this->version_php[1] && version_compare( phpversion(), $this->version_php[1], '>' ) )
        return new WP_Error( 'not-compatible', 'The plugin does not support your version of php.');


      $wp_version = get_bloginfo('version');

      if( version_compare( $wp_version, $this->version_wp[0], '<' ) )
        return new WP_Error( 'not-compatible', 'The plugin does not support your version of Wordpress.');

      else if( $this->version_wp[1] && version_compare( $wp_version, $this->version_wp[1], '>' ) )
        return new WP_Error( 'not-compatible', 'The plugin does not support your version of Wordpress.');

      return true;
    }

    function on_activation() {

      if( is_wp_error( $maybe_error = $this->is_compatible() ) )
       throw new Exception( $maybe_error -> get_error_message() );

    }

    function on_deactivation() {

    }

    function update() {

      add_filter( 'ohfuchs-tools/update', function( $updates ) {

        $updates[__FILE__] = array(
          'name' 				=> 'spreadshirt-for-wordpress',
          'type'				=> 'plugin',
          // master, beta, alpha
          'branch' 			=> apply_filters( 'plugintemplate/branch', 'master' ),
          'productkey' 	=> '',
          'license'			=> ''
        );

        return $updates;

      });

    }

  } // end class

  function sfw_plugin() {

    global $sfw_plugin;

    if( !$sfw_plugin instanceof SFW_Plugin ) {

      $sfw_plugin = new SFW_Plugin();
    }

    return $sfw_plugin;
  }

  sfw_plugin()->init();

} // end class_exists

