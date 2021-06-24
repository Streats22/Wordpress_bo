<?php if ( ! defined( '\ABSPATH' ) ) exit; ?>

<div class="wrap acf-settings-wrap">

	<h1><?php echo $page_title; ?></h1>

	<?php do_action( 'sfw_options_head'); ?>

	<form id="post" method="post" name="post">

		<?php

		if( $acf ) {

			acf_form_data(array(

				'screen'	=> 'options',

				'post_id'	=> $post_id,

			));

		}

		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );

		?>

		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

				<div id="postbox-container-1" class="postbox-container">

					<?php do_meta_boxes('sfw-admin-page-'.$menu_slug, 'side', null); ?>

				</div>

				<div id="postbox-container-2" class="postbox-container">

					<?php do_meta_boxes('sfw-admin-page-'.$menu_slug, 'normal', null); ?>

				</div>

			</div>

			<br class="clear">

		</div>

	</form>

	<?php do_action( 'sfw_options_footer'); ?>

</div>