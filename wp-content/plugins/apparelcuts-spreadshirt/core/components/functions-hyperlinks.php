<?php if ( ! defined( '\ABSPATH' ) ) exit;



/**
* Retrieve a proxied Spreadshirt Api Resource Url
*
* Links directly to the Rest API, therefor the user has to be logged in to view the resource.
*
* @param string $resource - the main name of the resource e.g. "articles", "productTypes"
* @param string $path - further path of the resource
* @param bool $append_default_params If locale, fullData and mediaType should be appended
* @since 1.0.0
*/

function sfw_get_shop_resource_url( $resource, $path = '', $append_default_params = false ){

  $shop = sfw_get_shop();

  if( !is_object( $shop ) || !property_exists( $shop, $resource )  )
    return '';

	$url = trailingslashit( sfw_get_shop()->{$resource}->href ) . $path;

	if( $append_default_params )
		$url.= '?fullData=true&locale='.sfw_get_locale().'&mediaType=json';

	return sfw_proxy_url( $url );
}




/**
* return Url to the Users Spreadshirt Platform
*
* @param string $path - a Path on the Spreadshirt Domain
* @return string
* @since 1.0.0
*/

function sfw_get_platform_url( $path = '' ) {

  $url = 'https://www.spreadshirt.'.sfw_get_shop_tld();

  if( !empty( $path ) && substr( $path, 0, 1) != '/' )
    $url = trailingslashit( $url );

  return $url.$path;
}




/**
* create an Anchor Tag
*
* @param array $args
* @return string
* @since 1.0.0
*/

function sfw_get_anchor_tag( $args ) {

  $args = wp_parse_args( $args, array(

    'class' => 'sfw-link',
    'href' => sfw_get_platform_url(),
    'target' => '_blank',
    'rel' => 'nofollow', // just in case
    'label' => __( 'Link', 'apparelcuts-spreadshirt' ),
    'before' => '',
    'after' => ''

  ));

  $attributes = $args;
  unset( $attributes['label'] );

  $anchor = $args['before'].'<a ';

  foreach( $attributes as $attr => $value )
    $anchor .= $attr . '="'. esc_attr( $value ). '" ';

  $anchor.= '>'. $args['label'] . '</a>'.$args['after'];

  return apply_filters( 'sfw/get_anchor_tag', $anchor, $args );
}




/**
* retrieve API resource URL for Article
*
* @param int $article_id - optional a Sfw Article Id
* @since 1.0.0
*/

function sfw_get_article_resource_url( $article_id = false ){

  return !empty( $article_id = sfw_get_article_id( $article_id ) )
    ? sfw_get_shop_resource_url( 'articles', $article_id, true)
    : false;
}




/**
* retrieve API resource URL for Product
*
* @param int $product_id - optional a Sfw Product Id
* @since 1.0.0
*/

function sfw_get_product_resource_url( $product_id = false ){

  return !empty( $product_id = sfw_get_product_id( $product_id ) )
    ? sfw_get_shop_resource_url( 'products', $product_id, true)
    : false;
}




/**
* retrieve API resource URL for Design
*
* @param int $design_id - optional a Sfw Design Id
* @since 1.0.0
*/

function sfw_get_design_resource_url( $design_id = false ){

  return !empty( $design_id = sfw_get_design_id( $design_id ) )
    ? sfw_get_shop_resource_url( 'designs', $design_id, true)
    : false;
}




/**
* retrieve API resource URL for ProductType
*
* @param int $producttype_id - optional a Sfw ProductType Id
* @since 1.0.0
*/

function sfw_get_producttype_resource_url( $producttype_id = false ){

  return !empty( $producttype_id = sfw_get_producttype_id( $producttype_id ) )
    ? sfw_get_shop_resource_url( 'productTypes', $producttype_id, true)
    : false;
}




/**
* retrieve API resource URL for PrintType
*
* @param int $printtype_id - optional a Sfw PrintType Id
* @since 1.0.0
*/

function sfw_get_printtype_resource_url( $printtype_id = false ){

  return !empty( $printtype_id = sfw_get_printtype_id( $printtype_id ) )
    ? sfw_get_shop_resource_url( 'printTypes', $printtype_id, true )
    : false;
}




/**
* retrieve an Anchor Tag with API resource URL for Article
*
* @param int $article_id - optional a Sfw Article Id
* @since 1.0.0
*/

function sfw_get_article_resource_link( $article_id = false, $args = array() ){

  if( empty( $url = sfw_get_article_resource_url( $article_id ) ) )
    return false;

  $args = wp_parse_args( $args, array(
    'label' => sprintf('%s %s', __('Article', 'apparelcuts-spreadshirt' ), __('API Endpoint', 'apparelcuts-spreadshirt' ) ),
    'href' => $url
  ) );

  return sfw_get_anchor_tag( $args );
}




/**
* retrieve an Anchor Tag with API resource URL for Product
*
* @param int $product_id - optional a Sfw Product Id
* @since 1.0.0s
*/

function sfw_get_product_resource_link( $product_id = false, $args = array() ){

  if( empty( $url = sfw_get_product_resource_url( $product_id ) ) )
    return false;

  $args = wp_parse_args( $args, array(
    'label' => sprintf('%s %s', __('Product', 'apparelcuts-spreadshirt' ), __('API Endpoint', 'apparelcuts-spreadshirt' ) ),
    'href' => $url
  ) );

  return sfw_get_anchor_tag( $args );
}



/**
* retrieve an Anchor Tag with API resource URL for Design
*
* @param int $design_id - optional a Sfw Design Id
* @since 1.0.0
*/

function sfw_get_design_resource_link( $design_id = false, $args = array() ){

  if( empty( $url = sfw_get_design_resource_url( $design_id ) ) )
    return false;

  $args = wp_parse_args( $args, array(
    'label' => sprintf('%s %s', __('Design', 'apparelcuts-spreadshirt' ), __('API Endpoint', 'apparelcuts-spreadshirt' ) ),
    'href' => $url
  ) );

  return sfw_get_anchor_tag( $args );
}




/**
* retrieve an Anchor Tag with API resource URL for ProductType
*
* @param int $producttype_id - optional a Sfw ProductType Id
* @since 1.0.0
*/

function sfw_get_producttype_resource_link( $producttype_id = false, $args = array() ){

  if( empty( $url = sfw_get_producttype_resource_url( $producttype_id ) ) )
    return false;

  $args = wp_parse_args( $args, array(
    'label' => sprintf('%s %s', __('Producttype', 'apparelcuts-spreadshirt' ), __('API Endpoint', 'apparelcuts-spreadshirt' ) ),
    'href' => $url
  ) );

  return sfw_get_anchor_tag( $args );
}




/**
* retrieve an Anchor Tag with API resource URL for PrintType
*
* @param int $producttype_id - optional a Sfw PrintType Id
* @since 1.0.0
*/

function sfw_get_printtype_resource_link( $printtype_id = false, $args = array() ){

  if( empty( $url = sfw_get_printtype_resource_url( $printtype_id ) ) )
    return false;

  $args = wp_parse_args( $args, array(
    'label' => sprintf('%s %s', __('Printtype', 'apparelcuts-spreadshirt' ), __('API Endpoint', 'apparelcuts-spreadshirt' ) ),
    'href' => $url
  ) );

  return sfw_get_anchor_tag( $args );
}




/**
* Get an Articles Platform Url
*
* @param int $article_id - optional a Sfw Article Id
* @since 1.0.0
*/

function sfw_get_article_platform_url( $article_id ) {

  return !empty( $article_id = sfw_get_article_id( $article_id ) )
    ? sfw_get_platform_url( 'slug-C2344/User/Products/details/article/'.$article_id )
    : false;
}




/**
* Get a ProductType Platform Url
*
* @param int $producttype_id - optional a Sfw ProductType Id
* @since 1.0.0
*/

function sfw_get_producttype_platform_url( $producttype_id ) {

  return !empty( $producttype_id = sfw_get_producttype_id( $producttype_id ) )
    ? sfw_get_platform_url( '/gestalten/detail/producttype-PT'.$producttype_id )
    : false;

}




/**
* retrieve an Anchor Tag with Article Platform Url
*
* @param int $producttype_id - optional a Sfw Article Id
* @since 1.0.0
*/

function sfw_get_article_platform_link( $article_id = false, $args = array() ) {

  if( empty( $article_id = sfw_get_article_id( $article_id ) ) )
    return false;

  $args = wp_parse_args( $args, array(
    'label' => __('Userarea', 'apparelcuts-spreadshirt' ),
    'href' => sfw_get_article_platform_url( $article_id )
  ) );

  return sfw_get_anchor_tag( $args );
}





/**
* retrieve an Anchor Tag with ProductType Platform Url
*
* @param int $producttype_id - optional a Sfw ProductType Id
* @since 1.0.0
*/

function sfw_get_producttype_platform_link( $producttype_id = false, $args = array() ) {

  if( empty( $producttype_id = sfw_get_producttype_id( $producttype_id ) ) )
    return false;

  $args = wp_parse_args( $args, array(
    'label' => __('Producttype Catalog', 'apparelcuts-spreadshirt' ),
    'href' => sfw_get_producttype_platform_url( $producttype_id )
  ) );

  return sfw_get_anchor_tag( $args );
}

