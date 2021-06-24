<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      1.0.0
 *
 * @package    Wp_Migration_Duplicator
 * @subpackage Wp_Migration_Duplicator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Migration_Duplicator
 * @subpackage Wp_Migration_Duplicator/admin
 * @author     WebToffee <support@webtoffee.com>
 */
class Wp_Migration_Duplicator_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/*
	 * module list, Module folder and main file must be same as that of module name
	 * Please check the `register_modules` method for more details
	 */
	public static $modules = array(
		'logger',
		'export',
		'import',
		'backups',
		'uninstall-feedback',
		'ftp',
		'googledrive',
		's3'
	);

	public static $existing_modules = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('wp_ajax_wp_mgdp_check_authentication', array($this, 'wt_migrator_check_authentication'));
		add_action('wp_ajax_wp_mgdp_populate_cloud_files', array($this, 'wt_migrator_populate_cloud_files'));
	}

	/**
	* @since 1.1.2
	* Admin page
	*/
	public function admin_settings_page()
	{
		// save settings
		include WT_MGDP_PLUGIN_PATH . '/admin/partials/wp-migration-duplicator-admin-display.php';
	}

	/**
	 * @since 1.1.8
	 * Admin page
	 */
	public function admin_storage_settings()
	{
		// save settings
		include WT_MGDP_PLUGIN_PATH . '/admin/partials/wp-migration-duplicator-admin-storage-display.php';
	}

	/**
	 * Generate tab head for settings page.
	 * method will translate the string to current language
	 * @since     1.1.2
	 */
	public static function generate_settings_tabhead($title_arr, $type = "plugin")
	{
		$out_arr = apply_filters("wt_mgdp_" . $type . "_settings_tabhead", $title_arr);
		foreach ($out_arr as $k => $v) {
			if (is_array($v)) {
				$v = (isset($v[2]) ? $v[2] : '') . $v[0] . ' ' . (isset($v[1]) ? $v[1] : '');
			}
?>
			<a class="nav-tab" href="#<?php echo $k; ?>"><?php echo $v; ?></a>
		<?php
		}
	}

	/**
	* @since 1.1.2
	* Admin menu hook
	*/
	public function admin_menu()
	{
		/*$menus = array(
			array(
				'menu',
				__('WordPress Migration', 'wp-migration-duplicator'),
				__('WordPress Migration', 'wp-migration-duplicator'),
				'manage_options',
				$this->plugin_name,
				array($this, 'admin_settings_page'),
				'dashicons-image-rotate-left',
				56
			)
		);*/
             $menus=array(
			array(
				'menu',
				__('Migration & Backup', 'wp-migration-duplicator'),
				__('WordPress Migration', 'wp-migration-duplicator'),
                                'manage_options',
                                $this->plugin_name,
                            	array($this,'admin_settings_page'),
				'dashicons-controls-repeat',
				56
			),
			array(
				'submenu',
				$this->plugin_name,
				__('Migration & Backup', 'wp-migration-duplicator'),
				__('Migration & Backup', 'wp-migration-duplicator'), 
				'manage_options',
				$this->plugin_name,
				array($this, 'admin_settings_page')
			),
		);
		$menus = apply_filters('wt_mgdp_admin_menu', $menus);
		if (count($menus) > 0) {
			foreach ($menus as $menu) {
				if ($menu[0] == 'submenu') {
					add_submenu_page($menu[1], $menu[2], $menu[3], $menu[4], $menu[5], $menu[6]);
				} else if ($menu[0] == 'menu') {
					add_menu_page($menu[1], $menu[2], $menu[3], $menu[4], $menu[5], $menu[6], $menu[7]);
				}
			}
		}

		if (function_exists('remove_submenu_page')) {
			//remove_submenu_page(WF_PKLIST_POST_TYPE,WF_PKLIST_POST_TYPE);
		}
	}

	/**
	 * Addding submenu items 
	 * @since 1.1.8
	 */

	function add_sub_menu_items()
	{

		add_submenu_page(
			$this->plugin_name,
			__('WordPress Migration Settings', 'wp-migration-duplicator'),
			__('Settings', 'wp-migration-duplicator'),
			'manage_options',
			$this->plugin_name . '-settings',
			array($this, 'admin_storage_settings')
		);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Migration_Duplicator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Migration_Duplicator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-migration-duplicator-admin.css', array(), $this->version, 'all');
		wp_register_style( 'select2css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css', false, '1.0', 'all' );
		wp_enqueue_style( 'select2css' );
		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Migration_Duplicator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Migration_Duplicator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_script( 'select2' );
		$params = array(
			'nonce'    => wp_create_nonce(WT_MGDP_PLUGIN_FILENAME),
			'ajax_url'  => admin_url('admin-ajax.php'),
			'messages' => array(
				'auth_error' => __('Authentication failed', 'wp-migration-duplicator'),
				'no_backups' => __('No backups found', 'wp-migration-duplicator'),
				'select_backup' => __('Please select a backup', 'wp-migration-duplicator'),
			),
			
		);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-migration-duplicator-admin.js', array('jquery','select2'), $this->version, false);
		wp_localize_script($this->plugin_name, 'wtMigratorObject', $params);
	}

	/**
	* @since 1.1.2
	* Registers modules: public+admin	 
	*/
	public function admin_modules()
	{
		$wt_mgdp_admin_modules = get_option('wt_mgdp_admin_modules');
		if ($wt_mgdp_admin_modules === false) {
			$wt_mgdp_admin_modules = array();
		}
		foreach (self::$modules as $module) //loop through module list and include its file
		{
			$is_active = 1;
			if (isset($wt_mgdp_admin_modules[$module])) {
				$is_active = $wt_mgdp_admin_modules[$module]; //checking module status
			} else {
				$wt_mgdp_admin_modules[$module] = 1; //default status is active
			}
			$module_file = plugin_dir_path(__FILE__) . "modules/$module/$module.php";
			if (file_exists($module_file) && $is_active == 1) {
				self::$existing_modules[] = $module; //this is for module_exits checking
				require_once $module_file;
			} else {
				$wt_mgdp_admin_modules[$module] = 0;
			}
		}
		$out = array();
		foreach ($wt_mgdp_admin_modules as $k => $m) {
			if (in_array($k, self::$modules)) {
				$out[$k] = $m;
			}
		}
		update_option('wt_mgdp_admin_modules', $out);
	}

	/**
	* @since 1.1.2
	* Sanitize input data 
	*/
	public static function sanitize_array($arr)
	{
		foreach ($arr as $key => $value) {
			$arr[$key] = sanitize_text_field($value);
		}
		return $arr;
	}

	/**
	 * @since 1.1.2 
	 * Envelope settings tab content with tab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_tabcontent($target_id, $view_file = "", $html = "", $variables = array(), $need_submit_btn = 0)
	{
		extract($variables);
		?>
		<div class="wf-tab-content" data-id="<?php echo $target_id; ?>">
			<?php
			if ($view_file != "" && file_exists($view_file)) {
				include_once $view_file;
				do_action('wt_migrator_after_setting_page_content_' . $target_id);
			} else {
				echo $html;
				do_action('wt_migrator_after_setting_page_content_' . $target_id);
			}
			?>
			<?php
			if ($need_submit_btn == 1) {
				include WT_MGDP_PLUGIN_PATH . "admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
<?php
	}


	/**
	 * helper function for getting storage options for import/export.
	 * @since 1.1.8
	 * @return Array $storage_options
	 */
	public function get_storage_options()
	{
		$storage_options = array(
			'local' => __('Local', 'wp-migration-duplicator')
		);

		return apply_filters('wt_migrator_storage_options', $storage_options);
	}
	/**
	 * Check if cloud storage is authenticated silently
	 * @since 1.1.8
	 * 
	 */
	public function wt_migrator_check_authentication()
	{

		if (!Wp_Migration_Duplicator_Security_Helper::check_write_access(WT_MGDP_PLUGIN_FILENAME, WT_MGDP_PLUGIN_FILENAME)) {
			wp_die(__('You do not have sufficient permission to perform this operation', 'wp-migration-duplicator'));
		}
		$cloud_storage_id = (isset($_POST['cloud_storage']) ? Wp_Migration_Duplicator_Security_Helper::sanitize_item($_POST['cloud_storage']) : '');
		$authenticated = apply_filters("wt_migrator_{$cloud_storage_id}_is_authenticated", false);
		if ($authenticated) {
			wp_send_json_success(__('Authentication success!', 'wp-migration-duplicator'));
		}
		wp_send_json_error(__('Authentication failed', 'wp-migration-duplicator'));
	}
	public function wt_migrator_populate_cloud_files() {
		if (!Wp_Migration_Duplicator_Security_Helper::check_write_access(WT_MGDP_PLUGIN_FILENAME, WT_MGDP_PLUGIN_FILENAME)) {
			wp_die(__('You do not have sufficient permission to perform this operation', 'wp-migration-duplicator'));
		}
		$cloud_storage_id = (isset($_POST['cloud_storage']) ? Wp_Migration_Duplicator_Security_Helper::sanitize_item($_POST['cloud_storage']) : '');
		$backup_files = apply_filters("wt_migrator_{$cloud_storage_id}_load_backups", false);
		
		if ( $backup_files ) {
			wp_send_json_success( $backup_files );
		}
		wp_send_json_error(__('No backups found', 'wp-migration-duplicator'));
	}
	/**
	 *  	Download file via a nonce URL
	 *	@since 1.1.6
	 */
	public function download_file()
	{
		if (isset($_GET['wt_mgdp_download'])) {
			if (Wp_Migration_Duplicator_Security_Helper::check_write_access(WT_MGDP_POST_TYPE)) { /* check nonce and role */ 
				$file_name = (isset($_GET['file']) ? sanitize_text_field($_GET['file']) : '');
				if ($file_name != "") {
					$file_arr = explode(".", $file_name);
					$file_ext = end($file_arr);
					if ($file_ext == 'zip') /* only zip files */ {
						$file_path = Wp_Migration_Duplicator::$backup_dir . '/' . $file_name;
						if (file_exists($file_path)) /* check existence of file */ {

							header('Pragma: public');
							header('Expires: 0');
							header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
							header('Cache-Control: private', false);
							header('Content-Transfer-Encoding: binary');
							header('Content-Disposition: attachment; filename="' . $file_name . '";');
							header('Content-Type: application/zip');
							header('Content-Length: ' . filesize($file_path));

							$chunk_size = 1024 * 1024;
							$handle = @fopen($file_path, 'rb');
							while (!feof($handle)) {
								$buffer = fread($handle, $chunk_size);
								echo $buffer;
								ob_flush();
								flush();
							}
							fclose($handle);
							exit();
						}
					}
				}
			}
			else {
				wp_die(__('You do not have sufficient permission to perform this operation', 'wp-migration-duplicator'));
			}
		}
	}

	/**
	 *  	Generate nonce URL for backup file
	 *	@since 1.1.6
	 *	@param string $file_name name of the file to be downloaded
	 */
	public static function generate_backup_file_url($file_name)
	{
		return wp_nonce_url(admin_url('admin.php?wt_mgdp_download=true&file=' . $file_name), WT_MGDP_POST_TYPE);
	}
}
