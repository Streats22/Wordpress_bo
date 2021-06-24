<?php if ( ! defined( '\ABSPATH' ) ) exit;


sfw_include( 'core/components/printtype/functions-printtype.php' );

sfw_include( 'core/components/printtype/printtype-taxonomy.php' );

sfw_include( 'core/components/printtype/functions-printtype-sync.php' );

sfw_include( 'core/components/printtype/printtype-sync.php' );

// save "Spreadshirt - Printtype Settings"
sfw_add_acf_group_save_point( 'group_559bab88681fe' );


sfw_register_entity( 'printtype', array(
  // post, term
  'wp_type'     => 'term',
  // posttype, taxonomy
  'wp_subtype'  => 'sfw-printtype',
  // metakey
  'wp_metakey'  => '_spreadshirt-id',
  // create_callback
  'create_callback' => 'sfw_create_printtype'
) );