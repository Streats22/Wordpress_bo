<?php if ( ! defined( '\ABSPATH' ) ) exit;


sfw_register_dynamic_page( array(
	'slug' 				 => 'confomat',
	'label' 			 => __( 'T-Shirt Designer', 'apparelcuts-spreadshirt' ),
  'instructions' => __( 'The main t-shirt designer', 'apparelcuts-spreadshirt' ),
	'post_name' 	 => sanitize_key( _x('shirt-designer', 'The confomat page slug', 'apparelcuts-spreadshirt' ) ),
  'post_content' => '',
  '_post_type' 	 => 'sfw-confomat'
) );
