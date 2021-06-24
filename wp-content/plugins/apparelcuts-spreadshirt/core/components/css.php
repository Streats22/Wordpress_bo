<?php if ( ! defined( '\ABSPATH' ) ) exit;




	/**
	 * Adds some default classes to the body
	 *
	 * @ignore
	 * @since 1.0.0
	 */

  add_filter( 'body_class', '_sfw_hook_body_class' );

	function _sfw_hook_body_class( $classes ) {

		$classes[] = 'sfw-basket-empty';
		$classes[] = 'sfw-no-js';

		return $classes;
	}






	/**
	 * Extend the post class
	 *
	 * @ignore
	 * @since 1.0.0
	 */

	function _sfw_hook_post_class( $classes, $class, $post_id ) {


		if( get_post_type( $post_id ) === 'sfw-product' ) {

			$classes[] = 'article-'.sfw_get_article_id();
			$classes[] = 'product-'.sfw_get_product_id();

		}

		elseif( get_post_type( $post_id ) === 'sfw-design' ) {

			$classes[] = 'design-'.sfw_get_design_id();

		}


		$classes[] = 'sfw-basket-empty';



		return $classes;

	}

  add_filter( 'post_class', '_sfw_hook_post_class', 10, 3 );




