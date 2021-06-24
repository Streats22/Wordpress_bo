<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Ads new Location for SFW Options Pages to ACF
 *
 * @since 1.0.0
 * @ignore
 */

class sfw_location_options_page extends acf_location {

	function initialize() {

		$this->name 		= 'sfw_options_page';
		$this->label 		= __( "SFW Options Page", 'apparelcuts-spreadshirt' );
		$this->category = 'forms';

	}

	function rule_match( $result, $rule, $screen ) {

		$options_page = acf_maybe_get( $screen, 'sfw_options_page' );

		return $this->compare( $options_page, $rule );

	}

	function rule_values( $choices, $rule ) {

		$pages = sfw_get_admin_pages();

		if( !empty($pages) )

			foreach( $pages as $page )

				$choices[ $page['menu_slug'] ] = $page['menu_title'];

		else

			$choices[''] = __('No options pages exist', 'apparelcuts-spreadshirt' );


    return $choices;

	}

}

if( function_exists('acf_register_location_rule' ) )
  acf_register_location_rule( 'sfw_location_options_page' );