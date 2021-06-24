<?php if ( ! defined( '\ABSPATH' ) ) exit;


sfw_include( 'core/components/admin-pages/class-admin-page-manager.php' );

sfw_include( 'core/components/admin-pages/class-admin-page-display.php' );

sfw_include( 'core/components/admin-pages/functions-admin-page.php' );

sfw_include( 'core/components/admin-pages/admin-pages-location-for-acf.php' );

// initialize displaying the admin page
new SFW_Admin_Page_Display();

