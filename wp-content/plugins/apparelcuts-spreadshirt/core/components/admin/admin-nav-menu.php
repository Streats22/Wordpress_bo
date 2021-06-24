<?php if ( ! defined( '\ABSPATH' ) ) exit;




/**
 * Registers nav menu link accordion box
 *
 * @ignore
 * @since 1.0.0
 */

function _sfw_hook_add_nav_menu_box( $object ) {

  if( !did_action( 'sfw/init' ) || !is_admin() )
    return;

	add_meta_box(
    'sfw-add-custom-links',
    _x( 'Spreadshirt Shop', 'Nav menu link suggestion box', 'apparelcuts-spreadshirt' ),
    '_sfw_callback_nav_menu_link_accordion_box',
    'nav-menus',
    'side',
    'default'
  );

	return $object;

}




/**
 * "Maybe you would have expected the add_meta_box function would be hooked to an action
 * like add_meta_boxes, which is triggered in post editing pages. Unfortunately, we don’t
 * have an action in nav-menus.php we can hook the function to, so we’re forced to use
 * the nav_menu_meta_box_object filter. This hook determines whether a menu item meta box
 * will be added for an object type. When the filter runs, add_meta_box registers the
 * custom meta box."
 *
 * @see https://kinsta.com/blog/wordpress-custom-menu/
 * @ignore
 */

add_filter( 'nav_menu_meta_box_object', '_sfw_hook_add_nav_menu_box', 10, 1);




/**
 * Creates box with suggested links
 *
 * @ignore
 * @since  1.0.0
 */

function _sfw_callback_nav_menu_link_accordion_box() {

  global $nav_menu_selected_id;



  // get menu items
  $menu_items = apply_filters( 'sfw/admin_nav_menu_items', array() );

  $menu_items = array_map( function( $menu_item ){

    $menu_item = wp_parse_args( (array)$menu_item, array(
      'db_id' => 0,
      'description' => '',
      'menu_item_parent' => 0,
      'type' => 'custom',
      'object' => 'custom',
      'object_id' => 'sfw-custom-link',
      'title' => '',
      'url' =>'#',
      'attr_title' => '',
      'classes' => array(),
      'target' => '',
      'xfn' => ''
    ) );

    return (object) $menu_item;

  }, $menu_items);

  $menu_items = array_map('wp_setup_nav_menu_item', $menu_items);

  // init walker
  $walker = new Walker_Nav_Menu_Checklist();


  ?>

	<div id="sfw-links" class="categorydiv">

    <div id="tabs-panel-sfw-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">

  		<ul id="sfw-links-checklist-all" class="categorychecklist form-no-clear">

  		    <?php	echo walk_nav_menu_tree( $menu_items, 0, (object) array( 'walker' => $walker) );?>

    	</ul>

		</div><!-- /.tabs-panel -->

		<p class="button-controls wp-clearfix">

			<span class="add-to-menu">

				<input type="submit" <?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?>
          class="button-secondary submit-add-to-menu right"
          value="<?php esc_attr_e('Add to Menu'); ?>"
          name="add-sfw-links-menu-item"
          id="submit-sfw-links" />

				<span class="spinner"></span>

			</span>

		</p>

	</div><!-- /.categorydiv -->

  <?php
}


/* default items */

add_filter( 'sfw/admin_nav_menu_items', function( $menu_items ){

  $menu_items[] = array(
    'description' => '',
    'object_id' => 'sfw-checkout',
    'title' => _x( 'Checkout', 'menu item label', 'apparelcuts-spreadshirt' ),
    'url' => '#checkout',
    'attr_title' => '',
    'classes' => array( 'sfw-checkout show-for-basket' ),
  );

  $menu_items[] = array(
    'description' => '',
    'object_id' => 'sfw-products',
    'title' => _x( 'All Products', 'menu item label', 'apparelcuts-spreadshirt' ),
    'url' => get_post_type_archive_link( 'sfw-product' ),
    'attr_title' => '',
    'classes' => array( '' ),
  );

  $menu_items[] = array(
    'description' => '',
    'object_id' => 'sfw-designs',
    'title' => _x( 'All Designs', 'menu item label', 'apparelcuts-spreadshirt' ),
    'url' => get_post_type_archive_link( 'sfw-design' ),
    'attr_title' => '',
    'classes' => array( '' ),
  );

  return $menu_items;
});