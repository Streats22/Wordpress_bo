<?php if ( ! defined( '\ABSPATH' ) ) exit;




/*
 * Make sure Designs are synced and exist before syncing Article
 */

add_filter( 'sfw/create/article/prepare', '_sfw_hook_sync_designs_before_article', 50, 4 );
add_filter( 'sfw/update/article/prepare', '_sfw_hook_sync_designs_before_article', 50, 4 );






/*
 * add design_ids to article
 */

add_filter( 'sfw/create/article', '_sfw_hook_sync_article_add_design_ids', 9, 3 );
add_filter( 'sfw/update/article', '_sfw_hook_sync_article_add_design_ids', 9, 3 );




