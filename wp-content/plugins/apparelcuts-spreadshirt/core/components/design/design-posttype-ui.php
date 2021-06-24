<?php if ( ! defined( '\ABSPATH' ) ) exit;




// Slug column

sfw_add_admin_table_column( 'sfw-design', __('Slug', 'apparelcuts-spreadshirt' ), function( $post_id ){

  $post = get_post( $post_id );

  echo $post->post_name;

});




// Description column

sfw_add_admin_table_column( 'sfw-design', __('Description', 'apparelcuts-spreadshirt' ), function( $post_id ){

	$post = get_post( $post_id );

	echo substr( strip_tags( $post->post_content ), 0 , 350 );

});




// Preview column

sfw_add_admin_table_column( 'sfw-design', __('Preview', 'apparelcuts-spreadshirt' ), function( $post_id ){

	$post = get_post( $post_id );

  sfw_preview_img( $post, array( 100, 100 ) );

  echo '<br/>';
  echo '<span style="color:#aaa;">'.sfw_get_design_id().'</span>';

});




// Groups and Tags column

sfw_add_admin_table_column( 'sfw-design', __('Groups and Tags', 'apparelcuts-spreadshirt' ), function( $post_id ){

	the_terms( $post_id, 'sfw-designgroup', '<h4 style="margin-top:0.5em; margin-bottom:0.5em;">'.__('Groups').'</h4><span>', ',', '</span>' );

	the_terms( $post_id, 'sfw-designtag' ,  '<h4 style="margin-top:0.5em; margin-bottom:0.5em;">'.__('Tags', 'apparelcuts-spreadshirt' ).'</h4><span>', ',', '</span>'  );

});




// Count column

sfw_add_admin_table_column( 'sfw-design', __('Article Count', 'apparelcuts-spreadshirt' ), function( $post_id ){

	$post = get_post( $post_id );
	$label =sfw_get_design_articles_total( $post );

	$design_edit_link = sfw_get_design_admin_edit_articles_link( $post, array( 'label' => $label ) );

	echo $design_edit_link;

});









/**
* Add custom post states to parent and child designs
*
* @ignore
* @since 1.0.0
*/

function _sfw_hook_add_design_relation_post_states( $post_states, $post ) {

	if( $post->post_type === 'sfw-design' ) {

		if( sfw_design_has_children( $post ) ) {

			$post_states[] = __('primary');
		}
		elseif( sfw_is_child_design( $post ) ) {

			$parent_design_id   = sfw_get_parent_design_id( $post );
			$parent_design_post = sfw_get_design_post( $parent_design_id );
			$post_states[]      = __('relative').' <a href="'.get_edit_post_link( $parent_design_post ).'">*</a>';
		}

	}
	return $post_states;
}

add_filter( 'display_post_states', '_sfw_hook_add_design_relation_post_states', 10, 2  );





/**
* Add Post row actions
*
* @ignore
* @since 1.0.0
*/

function _sfw_hook_sfw_design_post_row_actions( $actions, $post ) {


	if( $post->post_type != 'sfw-design')
		return $actions;


	$actions[] = sfw_get_design_admin_edit_articles_link( $post );

	$actions[] = sfw_get_design_resource_link( $post );


	return $actions;
}

add_filter( 'post_row_actions', '_sfw_hook_sfw_design_post_row_actions',	10,	2	);




/**
* Replaces the_title with the parent designs title if possible
*
* @ignore
* @since 1.0.0
*/

function _sfw_hook_design_admin_the_title( $title, $post_id ){

	if( sfw_current_screen_id_is( 'edit-sfw-design' ) ) {

		$design_post = get_post( $post_id );

		if( sfw_is_child_design( $design_post ) ) {

			$parent_design_post = sfw_get_wildcard_design_post( $design_post );
			$title = $parent_design_post->post_title;
		}
	}

	return $title;
}

add_action( 'the_title', '_sfw_hook_design_admin_the_title', 10, 2 );




/*
 * Add Metabox to Post Edit Screen
 */

add_action( 'add_meta_boxes', function() {

	$metabox = 	function ( $post ) {

		?>
		<div id="sfw-main-image" >
			<?php

			sfw_design_image( array(
				'width' => 400,
				'height' => 400,
				'extension' => 'png'
			))->img();
			?>
		</div>
		<?php
	};

	add_meta_box(
		'sfw-designdiv',
		_x( 'Design' , 'Designbild im Bearbeitenscreen'),
		$metabox,
		'sfw-design',
		'side',
		'high'
	);

}, 10	);




sfw_remember_field_key( 'field_561116ac0d446', 'sfw-background-color' );




/*
 * Add a design image after color select
 */
add_action( 'acf/render_field/key='.sfw_field_key('sfw-background-color'), function( $field ){

  ?><div class="sfw-design-background-color-example"><?php
  sfw_design_image( array(
    'width' => 100,
    'height' => 100,
    'extension' => 'png',
    'atts' => array( )
  ))->img();

	?>
  </div>
	<script>

	;(function($){

		acf.add_action('ready_field/type=color_picker', function( $el ){
			var picker = $('#acf-field_561116ac0d446');

			if ( picker.length) {
        $containers = $('#sfw-designdiv .inside, .sfw-design-background-color-example');

				picker.iris({
					//mode: 'hsv',
					//palettes: ['#e40571', '#4b63a4', '#ffcb05', '#fff', '#000'],
					change: function(event, ui){
						$containers.css( 'background-color', ui.color.toString() );
						//console.log( $(this), ui );
						$(this).parents('.wp-picker-container').find('.wp-color-result').css('background-color', ui.color.toString());
						$(this).parents('.wp-picker-container').find('.wp-color-picker').val(ui.color.toString());
					},
          clear : function(  ) {
          }
				});


				$containers.css( 'background-color', picker.iris('color') );
			}
		});

	})(jQuery);

	</script>
  <?php

}, 15 );




/**
 * Sync the choosen design background with the preview image
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_hook_acf_sync_design_iris( ) {

}

add_action( 'acf/input/admin_footer', '_sfw_hook_acf_sync_design_iris',	10 );




/*
 * Add a class to the post edit screen, editor and title are hidden with css
 */

add_action( 'admin_body_class', function ( $classes ) {

	if( sfw_current_screen_id_is( 'sfw-design' ) ) {

		if( sfw_is_child_design() ) {

			$classes .= ' sfw-design-has-parent sfw-design-child ';
		}
		elseif( sfw_design_has_children() ) {

			$classes .= ' sfw-design-is-parent sfw-design-has-children ';
		}
	}

	return $classes;

}	);




/*
 * notify the user about restricted editability of child designs
 */

add_action( 'admin_notices',	function (){


	if( !sfw_current_screen_id_is( 'sfw-design' ) )
		return;


	if( !sfw_is_child_design() )
		return;


	$link = sfw_get_anchor_tag( array(
		'href' => get_edit_post_link( sfw_get_design_post( sfw_get_parent_design_id() ) -> ID ),
		'label' => sfw_get_parent_design_id()
	));


	?><div class="update-nag">
		<strong><?php _e('Restricted Editability', 'apparelcuts-spreadshirt' );  ?></strong>
		<?php _e('This Design is marked as relative of another Design. Click here to edit the primary Design:', 'apparelcuts-spreadshirt' );  ?>
		<?php echo ' '.$link; ?>
	</div><?php

});


