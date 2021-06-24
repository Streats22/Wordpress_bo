<?php if ( ! defined( '\ABSPATH' ) ) exit;


// enqueue synchronization scripts and style
add_action( 'admin_enqueue_scripts/sfw-sync', function(){

  // sync tasker
  wp_enqueue_script( 'sfw-synchronization' );

  // style
  wp_enqueue_style( 'sfw-synchronization' );

  add_filter( 'sfw/wp_localize_script', function( $data ) {

    $data['local_timeout']  = get_field( 'sfw-sync-local-timeout', 'option' ) ?: 500;
    $data['remote_timeout'] = get_field( 'sfw-sync-remote-timeout', 'option' ) ?: 500;

    return $data;

  });
});



add_action( 'sfw/init', function(){

  // Create the synchronization Admin Page
  sfw_register_admin_page( array(

  	'page_title' 		=> __('Synchronization', 'apparelcuts-spreadshirt' ),

  	'menu_title'		=> _x('Synchronization', 'menu name', 'apparelcuts-spreadshirt' ),

  	'menu_slug' 		=> 'sfw-sync',

  	'acf' 					=> false

  ) );

});




// Add the Progress Meta box to Synchronization Admin Page

sfw_admin_page_add_metabox( 'sfw-sync',  array(

	'id' 				=> 'mb-sync-progress',

	//'title' 		=> __('Progress', 'apparelcuts-spreadshirt' ),

	'callback' 	=> function(){
    ?>

    <div id="sfw-sync">

      <div id="sfw-stage">
        <span class="ac ac-shirt"></span>
      </div>

      <div id="sfw-progress-primary" class="sfw-progress-bar">

        <div class="-sfw-label">
          <?php _e('Hit "Start" to begin!'); ?>
        </div>

        <div class="-sfw-bar">
          <div class="-sfw-progress"></div>
        </div>

      </div>

      <div id="sfw-progress-secondary" class="sfw-progress-bar">

        <div class="-sfw-bar">
          <div class="-sfw-progress"></div>
        </div>

        <div class="-sfw-label">
        </div>

      </div>

    </div>
    <?php

  }

));




// Add synchronization control button group metabox to the Synchronization Admin Page

sfw_admin_page_add_metabox( 'sfw-sync',  array(

	'id' 				=> 'mb-sync-control',

	'title' 		=> __('Synchronization', 'apparelcuts-spreadshirt' ),

  'context'   => 'side',

  'priority'  => 'high',

	'callback' 	=> function(){
    ?>
    <div class="sfw-sync-control">

      <span id="sfw-sync-start" class="button button-primary disabled"><?php _e( 'Start', 'apparelcuts-spreadshirt' ); ?></span>

      <span id="sfw-sync-stop" class="button  disabled "><?php _e( 'Stop', 'apparelcuts-spreadshirt' ); ?></span>

      <?php if( sfw_get_last_sync() > 0 ): ?>
        <p><input type="checkbox" id="force_update" /><label for="force_update"> <?php _e( 'Force Updates (slow)', 'apparelcuts-spreadshirt' ); ?></label>
      <?php endif;  ?>

      <?php if( sfw_get_last_sync() == 0 ): ?>

        <p><?php _e( 'Time to start your first Synchronization!', 'apparelcuts-spreadshirt' ); ?>

      <?php else: ?>

        <p><strong><?php _e( 'Last Sync:', 'apparelcuts-spreadshirt' ); ?> </strong><?php printf( _x( '%s ago', '%s human time diff', 'apparelcuts-spreadshirt' ), human_time_diff( sfw_get_last_sync() ) ); ?>

      <?php endif;  ?>
    </div>
    <?php
  }

));






// Add synchronization stats box

sfw_admin_page_add_metabox( 'sfw-sync',  array(

	'id' 				=> 'mb-sync-stats',

	'title' 		=> __('Statistics', 'apparelcuts-spreadshirt' ),

  'classes'    => array( 'sfw-hidden', 'apparelcuts-spreadshirt' ),

  'context'   => 'side',

  'priority'  => 'high',

	'callback' 	=> function(){
    ?>
    <div class="sfw-sync-stats">

    </div>
    <?php
  }

));





sfw_admin_page_add_metabox( 'sfw-sync',  array(
	'id' 				=> 'mb-welcome-meta',
	'title' 		=> 'Meta',
	'callback' 	=> '_sfw_callback_metabox_plugin_meta',
	'context'	=> 'side'
));


