<?php if ( ! defined( '\ABSPATH' ) ) exit;






function sfw_article_design_more(){

  if( ! sfw_get_design_id() )
    return;

  if( sfw_get_design_articles_count() > 1 ) {

    printf(
      '<a href="%s" class="article-design-more">%s &raquo;</a>',
      get_permalink( sfw_get_design_post() ),
      sprintf( _n( 'One Product with this Design', '%s Products with this Design', 'apparelcuts-spreadshirt' ), sfw_get_design_articles_count() )
    );
  }

}