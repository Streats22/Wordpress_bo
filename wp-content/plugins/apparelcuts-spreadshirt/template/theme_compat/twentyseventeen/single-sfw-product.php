<?php

add_filter( 'body_class', function( $classes ){

  if (($key = array_search('has-sidebar', $classes)) !== false) {
    unset($classes[$key]);
  }

  return $classes;
});

define( 'SFW_HIDE_HEADER', true );
define( 'SFW_HIDE_FOOTER', true );

?>
<?php get_header( 'sfw' ); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

      <?php sfw_include_template( 'single-sfw-product.php', false ); ?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer( 'sfw' ); ?>