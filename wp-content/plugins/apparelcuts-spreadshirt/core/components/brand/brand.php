<?php if ( ! defined( '\ABSPATH' ) ) exit;


sfw_include( 'core/components/brand/functions-brand.php' );

sfw_include( 'core/components/brand/brand-taxonomy.php' );

sfw_include( 'core/components/brand/functions-brand-sync.php' );

sfw_include( 'core/components/brand/brand-sync.php' );


sfw_register_entity( 'brand', array(

  // post, term
  'wp_type'     => 'term',

  // posttype, taxonomy
  'wp_subtype'  => 'sfw-brand',

  // metakey
  'wp_metakey'  => '_spreadshirt-id',

  // create Callback
  'create_callback' => 'sfw_create_brand'

) );
