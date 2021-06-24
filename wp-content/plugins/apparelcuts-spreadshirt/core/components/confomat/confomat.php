<?php if ( ! defined( '\ABSPATH' ) ) exit;

sfw_include( 'core/components/confomat/functions-confomat.php' );

sfw_add_acf_group_save_point( 'group_5c5ac36da0152' );

sfw_remember_field_key( 'group_5c5ac36da0152', 'group-confomat' );

sfw_include( 'core/components/confomat/confomat-posttype.php' );

sfw_include( 'core/components/confomat/confomat-integration.php' );

sfw_include( 'core/components/confomat/confomat-shortcode.php' );

sfw_include( 'core/components/confomat/confomat-ui.php' );

sfw_include( 'core/components/confomat/confomat-page.php' );