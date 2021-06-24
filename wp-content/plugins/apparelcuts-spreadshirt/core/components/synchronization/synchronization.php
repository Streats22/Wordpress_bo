<?php if ( ! defined( '\ABSPATH' ) ) exit;

// Helper functions
sfw_include( 'core/components/synchronization/functions-synchronization.php' );

// Create the Synchronization Screen
sfw_include( 'core/components/synchronization/synchronization-screen.php' );

// Register Synchronization Rest API
sfw_include( 'core/components/synchronization/synchronization-api.php' );


sfw_add_acf_group_save_point( 'group_5c94f2c162d43' );

sfw_remember_field_key( 'group_5c94f2c162d43', 'group-sync-settings' );