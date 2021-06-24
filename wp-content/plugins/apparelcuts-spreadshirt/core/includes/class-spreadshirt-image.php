<?php if ( ! defined( '\ABSPATH' ) ) exit;






/**
* Shorthand for creating Instances of Spreadshirt_Image Class
*
* @uses Spreadshirt_Image
*
* @param array $args See
*
* @return instance of spreadshirt image
*
* @since 1.0.0
*/

function spreadshirt_image( $args = array() ) {

	 return new Spreadshirt_Image( $args );
}




/**
* This Class creates IMG-Tags for Spreadshirt Images. They support srcset and respect the possible Spreadshirt Imagesizes.
*
* @since 1.0.0
*/

class Spreadshirt_Image {





	public $imageserver = array();
	public $settings		= array();



	/**
	* initialize, merge settings
	*
	* @since 1.0.0
	*/

	function __construct( $args = array() ) {

		$this->parse_args( $args );

	}



	/**
	* Parse Settings
	*
	* @since 1.0.0
	*/

	public function parse_args( $args ) {

		$defaults = array(
			'parts' 					=> array(),
			'query'						=> array(),
			'atts'						=> array(),
			'name'						=> '',
			'extension'				=> 'jpg',
			'lazy'						=> false,
			'imageserver' 		=> 'https://image.spreadshirtmedia.net/image-server/v1/',
			'width' 					=> 200,
			'height' 					=> 200
		);


		// extends only first time with defaults
		if( empty( $this->settings ) ) {
			$args = wp_parse_args( $args, $defaults );
		}

		$this->settings = wp_parse_args( $this->settings, $args );


		$this->settings['width'] = $this->get_closest_dimension( $this->settings['width'] );
		$this->settings['height'] = $this->get_closest_dimension( $this->settings['height'] );
		$this->ratio = $this->get_aspect_ratio( $this->settings['width'], $this->settings['height'] );
		$this->resolutions = $this->get_resolutions_with_ratio();

		$this->settings['query']['width'] = $this->settings['width'];
		$this->settings['query']['height'] = $this->settings['height'];

		return $this;
	}




	/**
	* get allowed spreadshirt image sizes
	*
	* @since 1.0.0
	*/

	private function get_allowed_dimensions(  ){

		return array( 35, 42, 50, 75, 100, 130, 150,
									190, 200, 250, 280, 300, 350,
									400, 450, 500, 550, 560, 600,
									650, 700, 750, 800, 850, 900,
									950, 1000, 1050, 1100, 1150, 1200 );
	}




	/**
	 * Retrieve possible resolutions that match ratio
	 *
	 * @return array
	 * @since  1.0.0
	 */

	function get_resolutions_with_ratio( ) {

		$resolutions = array();

		foreach( $this->get_allowed_dimensions() as $w ) {

			foreach( $this->get_allowed_dimensions() as $h ) {

				if( $this->ratio == $this->get_aspect_ratio( $w, $h ) ) {
					$resolutions[] = array( 'width' => $w, 'height' => $h );
				}
			}
		}

		return $resolutions;
	}




	/**
	 * If the choosen resolution is not supported by spreadshirt, choose the next best higher resolution
	 *
	 * @param  int $search
	 * @return int
	 * @since  1.0.0
	 */

	function get_closest_dimension( $search ) {

    foreach ( $this->get_allowed_dimensions() as $dimension ) {

			if( $dimension >= $search ) {
				return $dimension;
      }
    }

   return array_pop( $this->get_allowed_dimensions() );
	}




	/**
	* return image aspect ratio
	*
	* @since 1.0.0
	*/

	function get_aspect_ratio( $w, $h ){

		return round( $w / $h, 2 );
	}





	/**
	* Create the Url
	*
	* @since 1.0.0
	*/

	private function getSrc( $dimensions = array() ){

		// see ->parse_args
		extract( $this->settings );


		$url = trailingslashit( $imageserver );

		$url.= implode( '/', $parts );

		$url.= '.' . sanitize_key( $name );

		$url.= '.'.$extension;

		$url.= '?'.http_build_query( wp_parse_args( $dimensions, $query ) );

		return $url;

	}

	function src() {
		return $this->getSrc();
	}


	public function url( $temporary_args = array(), $echo = true ) {

		if( $echo )
			echo $this->src( $temporary_args );
		else
			return $this->src( $temporary_args );
	}


	public function link( $temporary_args = array(), $echo ) {

		$url  = $this->src( $temporary_args );
		$link = sprintf( '<a href="%s">%s</a>', esc_attr( $url ), $url );

		if( $echo )
			echo $link;
		else
			return $link;
	}




	/**
	 * Create srcset
	 *
	 * @since 1.0.0
	 */

	function getSrcset() {

		$srcset = array();

		foreach( $this->resolutions as $resolution ) {
			$srcset[] = sprintf( '%s %sw', $this->getSrc( $resolution ), $resolution['width'] );
		}

		return implode( ',', $srcset );
	}




	/**
	 * Echo srcset
	 *
	 * @since 1.0.0
	 */

	function srcset() {
		echo $this->getSrcset();
	}




	/**
	 * get sizes attribute
	 *
	 * @return string
	 * @since  1.0.0
	 */

	function getSizes() {

		// bail if sizes set manually
		if( @$this->settings['atts'][ 'sizes' ] ){
			return $this->settings['atts'][ 'sizes' ];
		}

		return sprintf( '(max-width: %1$spx) 100vw, %1$spx', $this->settings['width'] );
	}




	/**
	 * Get attribute string
	 *
	 * @param  array  $atts
	 * @return string
	 * @since  1.0.0
	 */

	function attributes( $atts = array() ) {

		$atts = wp_parse_args( $this->settings['atts'], $atts );
		$str  = '';

		foreach( $atts as $att => $value )
			$str .= sprintf(' %s="%s" ', sanitize_key( $att ), esc_attr( $value ) );

		return $str;
	}




	/**
	 * Get responsive html img tag
	 *
	 * @return string
	 * @since  1.0.0
	 */

	function image() {

		extract( $this->settings );

		$atts = $this->attributes( array(
			'srcset' => $this->getSrcset(),
			'sizes'  => $this->getSizes(),
			'class'  => 'spreadshirt-image',
			'title'  => $name,
			'alt'    => $name,
			'src'		 => $this->getSrc()
		) );

		$code = sprintf( '<img %s />', $atts );

		return $code;
	}




	/**
	 * Get unresponsive html img tag
	 *
	 * @return [type]
	 * @since  1.0.0
	 */

	function preview() {

		extract( $this->settings );

		$atts = $this->attributes( array(
			'class'  => 'spreadshirt-image',
			'alt'    => $name,
			'src'		 => $this->getSrc()
		) );

		$code = sprintf( '<img %s />', $atts );

		return $code;
	}




	/**
	 * Alias of image method
	 *
	 * @since  1.0.0
	 */

	function img() {
		echo $this->image();
	}




	/**
	* return div tag, can be used to make responsive background images
	*
	* @since 1.0.0
	*/

	function div() {

		extract( $this->settings );

		$atts = $this->attributes( array(
			'data-srcset' => $this->getSrcset(),
			'data-responsive-background-ratio' => $this->ratio,
			'class' 			 => 'responsive-image'
		) );

		$code = sprintf( '<div %s >', $atts );

		if( isset( $close_tag ) && $close_tag )
			$code.= '</div>';

		return $code;
	}

}


