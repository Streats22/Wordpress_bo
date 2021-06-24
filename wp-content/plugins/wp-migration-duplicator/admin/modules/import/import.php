<?php

/**
 * Import section of the plugin
 *
 * @link       
 * @since 1.1.2     
 *
 * @package  Wp_Migration_Duplicator  
 */
if (!defined('ABSPATH')) {
	exit;
}
class Wp_Migration_Duplicator_Import
{
	public $module_id = '';
	public static $module_id_static = '';
	public $module_base = 'import';
	public $step_list = array(
		'fetch_file',
		'start_import',
		'import_db',
	);
	public $ajax_action_list = array(
		'fetch_file',
		'start_import',
		'import_db',
	);
	public $button_click_enabled = false;
	public $attachment_url;

	public function __construct()
	{
		$this->module_id = Wp_Migration_Duplicator::get_module_id($this->module_base);
		add_action('wp_ajax_wt_mgdp_import', array($this, 'ajax_main'), 1);

		add_filter('wt_mgdp_plugin_settings_tabhead', array($this, 'settings_tabhead'));
		add_action('wt_mgdp_plugin_out_settings_form', array($this, 'out_settings_form'));
		add_action('wt_mgdp_backups_action_column', array($this, 'restore_backup_btn'), 10, 3);
		add_action('wt_mgdp_backups_table_top', array($this, 'restore_notice'), 10, 2);
	}

	/**
	 * 	@since 1.1.2
	 *	Showing a notice on top of the backup list table
	 */
	public function restore_notice($backup_list, $offset)
	{
?>
		<div class="wt_warn_box">
			<?php _e('Restoring from backup will overwrite all files in the system, including the existing backups. It will basically take you to the system state during backup.', 'wp-migration-duplicator'); ?>
			<br />
			<?php _e('Do not restore unless you are sure about what you are doing.', 'wp-migration-duplicator'); ?>
		</div>
		<?php
	}


	/**
	 * 	@since 1.1.2
	 *	Showing a restore button on restore list table (if file exists)
	 */
	public function restore_backup_btn($backup, $file_exists, $file_url)
	{
		if ($file_exists && $backup['status'] == Wp_Migration_Duplicator::$status_complete) {
		?>
			<button data-file-url="<?php echo $file_url; ?>" data-id="<?php echo $backup['id_wtmgdp_log']; ?>" title="<?php _e('Restore', 'wp-migration-duplicator'); ?>" class="button button-secondary wt_mgdp_restore_backup" style=""><span class="dashicons dashicons-update-alt" style="margin-top:4px;"></span></button>
		<?php
		}
		if (!$this->button_click_enabled) {
		?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('.wt_mgdp_restore_backup').unbind('click').click(function() {
						if (confirm('<?php _e('Are you sure?', 'wp-migration-duplicator'); ?>')) {
							var file_url = jQuery(this).attr('data-file-url');
							if (jQuery.trim(file_url) == "") {
								alert('<?php _e('Error', 'wp-migration-duplicator'); ?>');
								return false;
							}
							window.location.hash = "#wt-mgdp-import"; /* switching tab */
							jQuery('[name="attachment_url"]').val(file_url);
							jQuery('.wt_mgdp_import_attachment_url').html(file_url);
							jQuery('[name="wt_mgdp_import_btn"]').trigger('click');
						}
					});
				});
			</script>
<?php
			$this->button_click_enabled = true;
		}
	}

	/**
	 * All the possible Export Methods
	 * @since 1.1.8
	 */

	public static function get_possible_import_methods()
	{
		$import_methods = array(
			'local' => __('Local', 'wp-migration-duplicator')
		);

		return apply_filters('wt_mgdb_import_options', $import_methods);
	}

	/**
	 * @since 1.0.0
	 * Main ajax hook to handle all ajax requests
	 */
	public function ajax_main()
	{
		//sleep(1);

		ini_set('max_execution_time', 300);
		ini_set('memory_limit', '-1');
		set_time_limit(0);

		$action = sanitize_text_field($_POST['sub_action']);
		$out = array(
			'status' => false,
			'msg' => __('Error', 'wp-migration-duplicator'),
			'step_finished' => 0,
			'finished' => 0,
			'step' => $action,
			'label' => '',
			'sub_label' => '',
		);
		if (!Wp_Migration_Duplicator_Security_Helper::check_write_access(WT_MGDP_PLUGIN_FILENAME, $this->module_id)) {
			echo json_encode($out);
			exit();
		}
		if (in_array($action, $this->ajax_action_list) && method_exists($this, $action)) {
			$out = $this->{$action}($out);
		} else {
			//error
		}

		if ($out['step_finished'] == 1) //step finished move to next step
		{
			$step_array_key = array_search($action, $this->step_list);
			if (isset($this->step_list[$step_array_key + 1])) //next step exists
			{
				$out['step'] = $this->step_list[$step_array_key + 1];
			} else {
				$out['finished'] = 1;
				$out['label'] = '<span style="color:green; font-weight:bold;">' . __('Import completed.', 'wp-migration-duplicator') . '</span>';
			}
		}
		echo json_encode($out);
		exit();
	}

	/**
	 * @since 1.1.2
	 * import database
	 */
	private function import_db($out)
	{
		global $wpdb;
		/*  check backup file exists */
		$upload = wp_upload_dir();
		$db_directory_old = $upload['basedir'] ."/" . 'database.sql';
		$db_directory_new = Wp_Migration_Duplicator::$database_dir . "/" . 'database.sql';
		$filename = '';
		$file_found = false;
		if ( file_exists( $db_directory_new ) ) {
			$filename = $db_directory_new;
		} else if( file_exists( $db_directory_old ) ) {
			$filename = $db_directory_old;
		} else {
			$out['msg'] = __('Database backup file is missing. Unable to import database', 'wp-migration-duplicator');
			$out['sub_label'] = '<br /><span style="color:red;">' . $out['msg'] . '</span>';
			return $out;
		}
		/*  check DB connection is possible */
		$connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		@mysqli_query($connection, "SET FOREIGN_KEY_CHECKS = 0;");

		$mysql_version = substr(mysqli_get_server_info($connection), 0, 3); // Get Mysql Version
		if (mysqli_connect_errno()) {
			$out['msg'] = __('Unable to connect to database.', 'wp-migration-duplicator');
			$out['sub_label'] = '<br /><span style="color:red;">' . $out['msg'] . '</span>';
			return $out;
		}


		$templine = '';
		$error_count = 0;
		$non_error_count = 0;
		$fp = fopen($filename, 'r');
		// Loop through each line
		while (($line = fgets($fp)) !== false) {
			// Skip it if it's a comment
			if (((substr($line, 0, 2) == '--') && (!strstr($line, ')'))) || $line == '')
				continue;

			// Add this line to the current segment
			$templine .= $line;
			$templine = str_replace('webtoffee_', $wpdb->prefix, $templine);
			if ($mysql_version >= 5.5) {
				$templine = str_replace('utf8mb4_unicode_520_ci', 'utf8mb4_unicode_ci', $templine);
			}

			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -8, 8) == ';/*END*/') {
				// Perform the query
				if (!@mysqli_query($connection, $templine)) {
                                    $error_count++;
					Webtoffe_logger::error($connection->error);
				} else {
					$non_error_count++;
				}
				// Reset temp variable to empty
				$templine = '';
			}
		}

		@mysqli_query($connection, "SET FOREIGN_KEY_CHECKS = 1;");
		@mysqli_close($connection);
		fclose($fp);
		$database_directory = Wp_Migration_Duplicator::$database_dir;
		if (file_exists($database_directory)) {
			Wp_Migration_Duplicator::wt_mgt_delete_files($database_directory);
		}
		if ($non_error_count == 0) {
			$out['msg'] = __('Database import failed.', 'wp-migration-duplicator');
			$out['sub_label'] = '<br /><span style="color:red;">' . $out['msg'] . '</span>';
		} else {
			if ($error_count > 0) {
				$out['msg'] = __('Database imported.some queries are not executed properly', 'wp-migration-duplicator');
				$out['sub_label'] =  '<br />' . $out['msg'] . '<br />' . __('Import completed.', 'wp-migration-duplicator').'<br />' . __('Kindly use the login credentials of the imported site to log in.', 'wp-migration-duplicator');
			} else {
				$out['msg'] = __('Database imported.', 'wp-migration-duplicator');
				$out['sub_label'] = '<br />' . $out['msg'] . '<br />' . __('Import completed.', 'wp-migration-duplicator').'<br />' . __('Kindly use the login credentials of the imported site to log in.', 'wp-migration-duplicator');
				$out['label'] = '';
			}
			$out['step_finished'] = 1;
			$out['status'] = true;
		}
		return $out;
	}


	/**
	 * fetch the file
	 * @since 1.1.8
	 */

	public function fetch_file($out)
	{
		$options = Wp_Migration_Duplicator::get_webtoffee_migrator_option();
		$error_message = __('The specified file could not be found on your server ','wp-migration-duplicator');
		$import_result = array(
			'status' => false,
			'file' => '',
			'message' => $error_message
		);
		$import_method = Wp_Migration_Duplicator_Security_Helper::sanitize_item($_POST['import_method']);
		$attachment_url = Wp_Migration_Duplicator_Security_Helper::sanitize_item($_POST['attachment_url']);
		if( $attachment_url ) {
			$import_result['status'] = true;
			$import_result['file'] = $attachment_url;
			$import_result['message'] = __('File has fetched from the server, now importing the file....','wp-migration-duplicator');
		}
		$import_result = apply_filters('wt_migrator_get_import_attachment_url', $import_result,$import_method );
		$import_file = ( isset( $import_result['file'] ) ? $import_result['file'] : '' );

		if( $import_result['status'] === false ) {
			$error_message = ( isset( $import_result['message'] ) ? $import_result['message'] : '' );
			Webtoffe_logger::error($error_message);
			$out['msg'] = $error_message;
			$out['sub_label'] = '<br /><span style="color:red;">' . $out['msg'] . '</span>';
			$out['status'] = false;
			return $out;
		}
		$options['import_attachment_url'] = $import_file;

		Wp_Migration_Duplicator::update_webtoffee_migrator_option( $options );
		$out['step_finished'] = 1;
		$out['status'] = true;
		$out['msg'] = ( isset( $import_result['message'] ) ? $import_result['message'] : '' );
		$out['label'] = ( isset( $import_result['message'] ) ? $import_result['message'] : '' );
		$out['sub_label'] = '<br/>'.( isset( $import_result['message'] ) ? $import_result['message'] : '' );
		return $out;
	}

	/**
	 * 	@since 1.1.8
	 * 	start the import (Import files and directories)
	 *
	 */
	private function start_import($out)
	{	
		$options = Wp_Migration_Duplicator::get_webtoffee_migrator_option();
		$extract_to = WP_CONTENT_DIR;
		$attachment_url = Wp_Migration_Duplicator_Security_Helper::sanitize_item( $options['import_attachment_url']);
		$parse_url = parse_url($attachment_url);
		$real_url = $_SERVER['DOCUMENT_ROOT'] . ($parse_url['path']);
		if (!strpos($real_url, '.zip')) {
			$out['msg'] = __("Please upload Zip file", 'wp-migration-duplicator');
			$out['sub_label'] = '<br /><span style="color:red;">' . $out['msg'] . '</span>';
			return $out;
		}


		/* extracting zip file */
		if(extension_loaded('zip')){
			$zip	=	new ZipArchive;
			if ($zip->open($real_url)) {
				$zip->extractTo($extract_to);
				$imported	= $zip->close();
				$out['status'] = true;
				$out['step_finished'] = 1;
				$out['label'] = __('Importing database....', 'wp-migration-duplicator');
				$out['sub_label'] = '<br />' . __('Files imported.', 'wp-migration-duplicator') . '<br />' . __('Importing database....', 'wp-migration-duplicator');
			} else {
				$error_message = "Cannot open " . $real_url . " for writing.";
				Webtoffe_logger::error($error_message);
				$out['status'] = false;
				$out['step_finished'] = 0;
				$out['label'] = __('Somehting went wrong!', 'wp-migration-duplicator');
				$out['sub_label'] = '<br />' . __('Can\'t open zip file.', 'wp-migration-duplicator');
			}
			
		}else{
			if (!class_exists('PclZip')) {
					include ABSPATH . 'wp-admin/includes/class-pclzip.php';
				 }
				$archive = new PclZip($real_url);
				if ($archive->extract(PCLZIP_OPT_PATH, $extract_to) == 0) {
					$out['status'] = false;
					$out['step_finished'] = 0;
					$out['label'] = __('Somehting went wrong!', 'wp-migration-duplicator');
					$out['sub_label'] = '<br />' . __('Can\'t open zip file.', 'wp-migration-duplicator');
				}else{
					$out['status'] = true;
					$out['step_finished'] = 1;
					$out['label'] = __('Importing database....', 'wp-migration-duplicator');
					$out['sub_label'] = '<br />' . __('Files imported.', 'wp-migration-duplicator') . '<br />' . __('Importing database....', 'wp-migration-duplicator');

				}
				
		}
		return $out;
	}

	/**
	 *  @since 1.1.2
	 * 	Import tab head filter callback
	 **/
	public function settings_tabhead($arr)
	{
		$out = array();
		$added = 0;
		foreach ($arr as $k => $v) {
			$out[$k] = $v;
			if ($k == 'wt-mgdp-export') //add after export
			{
				$out['wt-mgdp-import'] = __('Import', 'wp-migration-duplicator');
				$added = 1;
			}
		}
		if ($added == 0) //no export menu, then add it as first item
		{
			$out = array_merge(array('wt-mgdp-import' => __('Import', 'wp-migration-duplicator')), $arr);
		}
		return $out;
	}

	/**
	 *  @since 1.1.2
	 * 	Import page tab content filter callback
	 **/
	public function out_settings_form($arr)
	{
		wp_enqueue_script($this->module_id, plugin_dir_url(__FILE__) . 'assets/js/main.js', array('jquery'), WP_MIGRATION_DUPLICATOR_VERSION);
		/* enque media library */
		wp_enqueue_media();
		$params = array(
			'nonces' => array(
				'main' => wp_create_nonce($this->module_id),
			),
			'ajax_url' => admin_url('admin-ajax.php'),
			'labels' => array(
				'error' => sprintf(__('An unknown error has occurred! Refer to our %stroubleshooting guide%s for assistance.'), '<a href="'.WT_MGDP_PLUGIN_DEBUG_BASIC_TROUBLESHOOT.'" target="_blank">', '</a>'),
				'success' => __('Success', 'wp-migration-duplicator'),
				'finished' => __('Finished', 'wp-migration-duplicator'),
				'sure' => __("You can't undo this action. Are you sure?", 'wp-migration-duplicator'),
				'saving' => __("Saving", 'wp-migration-duplicator'),
				'connecting' => __("Connecting....", 'wp-migration-duplicator'),
				'backupfilenotempty' => __("Please upload a backup file.", 'wp-migration-duplicator'),
				'onlyzipfile' => __("Please upload a zip file.", 'wp-migration-duplicator'),
				'noprofile' => __("Please select FTP profile.", 'wp-migration-duplicator'),
				'pathrequired' => __("Please specify the path.", 'wp-migration-duplicator'),
				'nofilename' => __("Please specify the file name.", 'wp-migration-duplicator'),
				'zip_disable' => __("Before import Please enable ZipArchive extension in server", 'wp-migration-duplicator'),
			)
		);
		wp_localize_script($this->module_id, $this->module_id, $params);

		$view_file = plugin_dir_path(__FILE__) . 'views/importer.php';
		$params = array();
		Wp_Migration_Duplicator_Admin::envelope_settings_tabcontent('wt-mgdp-import', $view_file, '', $params, 0);
	}
}
new Wp_Migration_Duplicator_Import();
