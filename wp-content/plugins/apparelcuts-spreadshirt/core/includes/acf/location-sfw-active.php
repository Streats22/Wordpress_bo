<?php if ( ! defined( '\ABSPATH' ) ) exit;

/**
 * Ads new Location for SFW Options Pages to ACF
 *
 * @since 1.0.0
 * @ignore
 */

if( class_exists( 'acf_location' ) ):

class sfw_location extends acf_location {

	function initialize() {

		$this->name 		= 'sfw_status';
		$this->label 		= __( "SFW Status", 'apparelcuts-spreadshirt' );
		$this->category = 'forms';

	}

	function rule_match( $result, $rule, $screen ) {


    switch( $rule['value'] ) {

      case 'is_configured':

        $result = sfw_is_shop_properly_configured();

      break;

      case 'is_synced':

        $result = sfw_is_synced();

      break;

      case 'did_init':

        $result = did_action( 'sfw/init' );

      break;

      default :

        return false;

      break;

    }


    if( $rule['operator'] == '==' )
      return $result;


    if( $rule['operator'] == '!=' )
      return !$result;


    return false;

		//return $this->compare( $options_page, $rule );

	}

	function rule_values( $choices, $rule ) {

    $choices = array(
      'is_configured' => 'is configured',
      'is_synced'     => 'was synced at least once',
      'did_init'      => 'did the init action',
    );

    return $choices;

	}

}

if( function_exists('acf_register_location_rule' ) )
  acf_register_location_rule( 'sfw_location' );


endif; // end class_exists