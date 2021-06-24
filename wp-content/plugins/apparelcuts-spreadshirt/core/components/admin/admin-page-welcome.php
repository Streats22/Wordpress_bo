<?php if ( ! defined( '\ABSPATH' ) ) exit;

  /* create the Main Menu and Welcome Page */



// make it the main page
add_filter( 'sfw/parent_menu_slug', function(){ return 'sfw-welcome'; });



// add admin page
sfw_register_admin_page( array(

	'page_title' 		=> __('Welcome to Spreadshirt for Wordpress by Apparelcuts!', 'apparelcuts-spreadshirt' ),

	'menu_title'		=> __('Spreadshirt', 'apparelcuts-spreadshirt' ),

	'menu_slug' 		=> 'sfw-welcome',

	'icon_url' 		=> 'none',
	//'capability'		=> 'manage_options',

	//'metabox'				=> 'sfw_welcome_page',

	'acf' 					=> false

) );




/**
* Retrieve the Parent Page Url
*
* @see sfw_admin_get_parent_page_url
* @return string the parent page url
* @since 1.0.0
*/

function sfw_admin_get_welcome_page_url() {

	return sfw_admin_get_parent_page_url();
}


/**
* Retrieve the Parent Page Url
*
* @return string
*/

function sfw_admin_get_welcome_page_slug() {

	return sfw_admin_get_parent_page_url();
}


sfw_admin_page_add_metabox( 'sfw-welcome',  array(
	'id' 				=> 'welcome-first-steps',
	'title' 		=> __('First Steps', 'apparelcuts-spreadshirt' ),
	'callback' 	=> '_sfw_callback_metabox_first_steps'
));




function _sfw_callback_metabox_first_steps() {

	?>
	<h3><?php _e('Hi! Thank you for using our Plugin!', 'apparelcuts-spreadshirt' ); ?></h3>

	<p><?php _e("Here are a few things, that may help you to get started. Don't forget to checkout the documentation and if you have any questions or feedback, please check out the forums.", 'apparelcuts-spreadshirt' ); ?></p>

	<?php if(! sfw_is_synced() ): ?>

		<h4><?php _e('First steps to setup your shop.', 'apparelcuts-spreadshirt' ); ?></h4>

		<ol>

			<li>
				<?php _e( "Register for an API key with your Spreadshirt user id in case you don't have one yet.", 'apparelcuts-spreadshirt' ); ?>
				<br/>
				<a href="https://www.spreadshirt.net/userarea/-C7120" target="_blank">Spreadshirt (EU)</a>
				&middot;
				<a href="https://www.spreadshirt.com/userarea/-C6840" target="_blank">Spreadshirt (NA)</a>
			</li>

			<li><?php _e('Check the settings page and enter your Shop Id and API Credentials.', 'apparelcuts-spreadshirt' ); ?></li>

			<li><?php _e('Do your first synchronization.', 'apparelcuts-spreadshirt' ); ?></li>

		</ol>

	<?php endif; //is_synced  ?>

	<?php

}


sfw_admin_page_add_metabox( 'sfw-welcome',  array(
	'id' 				=> 'mb-welcome-meta',
	'title' 		=> 'Meta',
	'callback' 	=> '_sfw_callback_metabox_plugin_meta',
	'context'	=> 'side'
));



function _sfw_callback_metabox_plugin_meta() {

	?>

	<img src="<?php echo sfw_url('assets/images/apparelcuts-logo.png'); ?>" id="apparelcuts-logo"/>
	<h3>Spreadshirt for Wordpress</h3>
	<h4>Version <?php echo sfw_version();?></h4>

	<p><?php _e('Synchonize your Spreadshirt Shop with your Wordpress Database', 'apparelcuts-spreadshirt' ); ?></p>

	<?php do_action('sfw/metabox/plugin_meta'); ?>

	<ul>
		<li>
			<a href="https://www.apparelcuts.com/resources/spreadshirt-for-wordpress-plugin/" target="_blank">
				<i class="dashicons dashicons-external"></i> Pro Version
			</a>
		</li>
		<li>
			<a href="https://www.apparelcuts.com/forums" target="_blank">
				<i class="dashicons dashicons-external"></i> Support
			</a>
		</li>
		<li>
			<a href="https://www.apparelcuts.com/docs/spreadshirt-for-wordpress/" target="_blank">
				<i class="dashicons dashicons-external"></i> <?php _e('Documentation', 'apparelcuts-spreadshirt' ); ?>
			</a>
		</li>
		<li>
			<a href="https://www.apparelcuts.com/blog" target="_blank">
				<i class="dashicons dashicons-external"></i> Blog
			</a>
		</li>
		<li>
			<a href="https://www.apparelcuts.com/privacy-policy" target="_blank">
				<i class="dashicons dashicons-external"></i> <?php _e('Privacy Policy', 'apparelcuts-spreadshirt' ); ?>
			</a>
		</li>
		<li class="" style="margin-top:1rem;">
			<strong>Support us</strong>
		</li>
		<li>
			<a href="https://www.patreon.com/apparelcuts" target="_blank">
				<i class="dashicons dashicons-external"></i> Patreon
			</a>
		</li>
		<li>
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JNRWNRFG7U9ZL&source=url" target="_blank">
				<i class="dashicons dashicons-external"></i> <?php _e('Donate via PayPal', 'apparelcuts-spreadshirt' ); ?>
			</a>
		</li>
	</ul>
	<?php

}


function _sfw_hook_metabox_patreon() {

	?>
	<div class="patreon-support-claim">
		<p><?php _e('The Development of this plugin takes a lot of time, I would appreciate when you consider supporting it via Patreon. As Patron you receive a lot of benefits including priority support.', 'apparelcuts-spreadshirt' ); ?>
		<p><a href="https://www.patreon.com/apparelcuts" target="_blank">
			<img src="<?php echo sfw_url('assets/images/become_a_patron_button.png'); ?>" id="apparelcuts-logo"/>
		</a>
	</div>
	<?php

}

add_action( 'sfw/metabox/plugin_meta', '_sfw_hook_metabox_patreon' );





/*

sfw_admin_page_add_metabox( 'sfw-welcome',  array(
	'id' 				=> 'welcome-changelog',
	'title' 		=> __('Recent Changes', 'apparelcuts-spreadshirt' ),
	'callback' 	=> 'sfw_welcome_page_changelog'
));


function sfw_welcome_page_changelog() {

}

*/