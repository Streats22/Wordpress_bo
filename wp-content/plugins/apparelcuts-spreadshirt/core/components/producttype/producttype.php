<?php if ( ! defined( '\ABSPATH' ) ) exit;

sfw_include( 'core/components/producttype/functions-producttype.php' );

sfw_include( 'core/components/producttype/functions-producttype-appearances.php' );

sfw_include( 'core/components/producttype/functions-producttype-sizes.php' );

sfw_include( 'core/components/producttype/functions-producttype-views.php' );

sfw_include( 'core/components/producttype/producttype-taxonomy.php' );

sfw_include( 'core/components/producttype/functions-producttype-sync.php' );

sfw_include( 'core/components/producttype/producttype-sync.php' );



sfw_register_entity( 'producttype', array(

  // post, term
  'wp_type'     => 'term',

  // posttype, taxonomy
  'wp_subtype'  => 'sfw-producttype',

  // metakey
  'wp_metakey'  => '_spreadshirt-id',

  // create Callback
  'create_callback' => 'sfw_create_producttype',

  // expiration
  'sync_expire' => WEEK_IN_SECONDS

) );

