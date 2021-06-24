<?php if ( ! defined( '\ABSPATH' ) ) exit;


/**
 * sync brand before producttype gets synced
 *
 * producttypes always sync before articles, so we don't add extra logic for this
 *
 * @ignore
 * @since 1.0.0
 */

function _sfw_hook_sync_brand_before_producttype( $bool, $entity, $producttype_id ){


  if( true !== $bool  )
    return $bool;


  if( is_wp_error( $producttype = sfw_get_producttype( $producttype_id, false, true ) ) )
    return $producttype;


  $result = sfw_sync_item( 'brand', $producttype->brand );


  return is_wp_error( $result ) ? $result : true;

}

add_filter( 'sfw/create/producttype/prepare', '_sfw_hook_sync_brand_before_producttype', 100, 3 );
add_filter( 'sfw/update/producttype/prepare', '_sfw_hook_sync_brand_before_producttype', 100, 3 );







/*
 * add brand_term to article if not exists
 */

add_filter( 'sfw/create/article', '_sfw_hook_sync_article_add_brand_terms', 9, 4 );
add_filter( 'sfw/update/article', '_sfw_hook_sync_article_add_brand_terms', 9, 4 );
