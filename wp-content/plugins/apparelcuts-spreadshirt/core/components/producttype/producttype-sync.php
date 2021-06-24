<?php if ( ! defined( '\ABSPATH' ) ) exit;




/*
* Make sure Producttype is synced and exists before syncing Article
*/

add_filter( 'sfw/create/article/prepare', '_sfw_hook_sync_producttype_before_article', 100, 3 );
add_filter( 'sfw/update/article/prepare', '_sfw_hook_sync_producttype_before_article', 100, 3 );




/*
* add producttype_term to article if not exists
*/

add_filter( 'sfw/create/article', '_sfw_hook_sync_article_add_producttype_terms', 9, 3 );
add_filter( 'sfw/update/article', '_sfw_hook_sync_article_add_producttype_terms', 9, 3 );
