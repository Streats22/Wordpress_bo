<?php if ( ! defined( '\ABSPATH' ) ) exit;



	/**
	* Add Metabox to Post Edit Screen
	*/

  add_action( 'add_meta_boxes', function (){

    add_meta_box(
      'sfw-productdiv',
      __( 'Preview' , 'apparelcuts-spreadshirt' ),
      function ( $post ) {

        ?>
        <div id="sfw-main-image">
          <?php echo sfw_article_image( array(400, 400) )->preview(); ?>
        </div>
        <?php while( sfw_have_views() ): ?>
          <div class="sfw-sub-image">
            <?php echo sfw_article_image( array(400, 400) )->preview(); ?>
          </div>
        <?php endwhile; ?>
        <script type="text/javascript">
          (function($){
            $(document).ready(function(){
              $('.sfw-sub-image').on('click', function(){
                $('#sfw-main-image img').attr('src', $('img', this).attr('src'));
              });
            });
          })(jQuery);
        </script>
        <?php



        //while( sfw_have_designs()) {
        //  var_dump( sfw_get_current_design() );
        //}
      },
      'sfw-product',
      'side',
      'high'
    );
  });






// Preview column

sfw_add_admin_table_column( 'sfw-product', __('Spreadshirt ID', 'apparelcuts-spreadshirt' ), function( $post_id ){

  printf( '<span title="%s" style="color:#aaa;">%s</span>', __('Product ID', 'apparelcuts-spreadshirt' ), sfw_get_product_id() );
  printf( '<br/><span title="%s" style="color:#aaa;">%s</span>', __('Article ID', 'apparelcuts-spreadshirt' ), sfw_get_article_id() );

});




// Preview column

sfw_add_admin_table_column( 'sfw-product', __('Preview', 'apparelcuts-spreadshirt' ), function( $post_id ){

	$post = get_post( $post_id );

  sfw_preview_img( $post, array( 100, 100 ) );

});




// Preview column

sfw_add_admin_table_column( 'sfw-product', __('Designs', 'apparelcuts-spreadshirt' ), function( $post_id ){

	while( sfw_have_designs() ) {

		$design_post = sfw_get_design_post( sfw_get_current_design_id() );

		echo sfw_get_anchor_tag( array(
			'href' =>	sfw_get_design_admin_edit_articles_url(),
			'title' => __('Edit Design', 'apparelcuts-spreadshirt' ),
			'label' => 	sfw_create_preview_img( $design_post )
		));

	}

});








	/**
	* Add Post row actions
	*/

  add_filter( 'post_row_actions', function ( $actions, $post ) {

    if( $post->post_type != 'sfw-product')
      return $actions;


    $actions[] = sfw_get_producttype_platform_link( $post );
    $actions[] = sfw_get_article_platform_link( $post );
    $actions[] = sfw_get_product_resource_link( $post );
    $actions[] = sfw_get_article_resource_link( $post );


    return $actions;


  }, 	10, 	2 	);








