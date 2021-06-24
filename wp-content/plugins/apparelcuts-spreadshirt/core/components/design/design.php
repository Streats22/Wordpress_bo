<?php if ( ! defined( '\ABSPATH' ) ) exit;


sfw_include( 'core/components/design/functions-design.php' );

sfw_include( 'core/components/design/design-posttype.php' );

sfw_include( 'core/components/design/design-posttype-ui.php' );

sfw_include( 'core/components/design/design-posttype-relation.php' );

sfw_include( 'core/components/design/design-posttype-query.php' );

sfw_include( 'core/components/design/functions-design-sync.php' );

sfw_include( 'core/components/design/design-sync.php' );

sfw_include( 'core/components/design/designgroup-taxonomy.php' );

sfw_include( 'core/components/design/designtag-taxonomy.php' );


// save "Advanced Design Settings"
sfw_add_acf_group_save_point( 'group_56136ccd0728f' );

sfw_remember_field_key( 'field_5749cf3d26cd3', 'sfw-parent-design-post-id' );
sfw_remember_field_key( 'field_56137a0ca5739', 'sfw-parent-design' );

sfw_register_entity( 'design', array(

  // post, term
  'wp_type'     => 'post',

  // posttype, taxonomy
  'wp_subtype'  => 'sfw-design',

  // metakey
  'wp_metakey'  => '_design-id',

  // create_callback
  'create_callback' => 'sfw_create_design'

) );


