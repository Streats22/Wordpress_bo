<?php if ( ! defined( '\ABSPATH' ) ) exit;

sfw_include( 'core/components/product-article/functions-product.php' );

sfw_include( 'core/components/product-article/functions-product-configurations.php' );

sfw_include( 'core/components/product-article/functions-article.php' );

sfw_include( 'core/components/product-article/functions-article-price.php' );

sfw_include( 'core/components/product-article/functions-article-designs.php' );

sfw_include( 'core/components/product-article/product-article-posttype.php' );

sfw_include( 'core/components/product-article/product-article-posttype-ui.php' );

sfw_include( 'core/components/product-article/functions-product-article-sync.php' );

sfw_include( 'core/components/product-article/productgroup-taxonomy.php' );



sfw_register_entity( 'article', array(
  // post, term
  'wp_type'     => 'post',
  // posttype, taxonomy
  'wp_subtype'  => 'sfw-product',
  // metakey
  'wp_metakey'  => '_article-id',
  // create Callback
  'create_callback' => 'sfw_create_article'
) );


